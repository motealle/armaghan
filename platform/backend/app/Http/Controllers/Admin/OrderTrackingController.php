<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\TrackedOrder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class OrderTrackingController extends Controller {
    public const STAGES=['inquiry','review','invoice','awaiting_deposit','production','quality','ready','shipped','delivered','cancelled'];
    public const PATHS=['simple','available','unavailable','custom','brand','packaging'];
    private function admin(Request $request): bool { return $request->attributes->get('armaghan.customer') === null; }
    public function index(Request $request): JsonResponse {
        $data=$request->validate(['page'=>['sometimes','integer','min:1']]);
        $query=TrackedOrder::query();
        if (!$this->admin($request)) $query->where('customer_id',$request->attributes->get('armaghan.customer')->id);
        elseif (!$request->user()->isPrimaryOwner()) $query->whereNotIn('customer_id',Customer::whereHas('user',fn($q)=>$q->whereRaw('lower(email) = ?',[config('owner-access.primary_owner_email')]))->select('id'));
        $page=$query->orderByDesc('id')->paginate(25);
        return $this->json(['orders'=>$page->getCollection()->map(fn($o)=>$this->snapshot($o,$this->admin($request))),'page'=>$page->currentPage(),'last_page'=>$page->lastPage()]);
    }
    public function store(Request $request): JsonResponse {
        $admin=$this->admin($request);
        $data=$request->validate(['customer_id'=>[$admin?'required':'prohibited','integer','exists:customers,id'],
            'request_path'=>['required',Rule::in(self::PATHS)],'description'=>['required','string','min:3','max:4000'],'request_key'=>['sometimes','required','uuid']]);
        $customer=$admin?Customer::findOrFail($data['customer_id']):$request->attributes->get('armaghan.customer');
        if ($admin) $this->guardCustomer($request,$customer);
        abort_unless($customer->active,422);
        $order=DB::transaction(function () use($request,$data,$customer,$admin) {
            $scope=hash('sha256',($admin?'admin:'.$request->user()->id:'customer:'.$customer->id));
            $payload=hash('sha256',json_encode([$customer->id,$data['request_path'],$data['description']],JSON_THROW_ON_ERROR));
            $key=null;
            if(isset($data['request_key'])){
                DB::table('order_submission_keys')->insertOrIgnore(['scope_key'=>$scope,'request_key'=>$data['request_key'],'payload_hash'=>$payload,'created_at'=>now(),'updated_at'=>now()]);
                $key=DB::table('order_submission_keys')->where('scope_key',$scope)->where('request_key',$data['request_key'])->lockForUpdate()->first();
                abort_unless($key&&hash_equals($key->payload_hash,$payload),409);
                if($key->tracked_order_id)return TrackedOrder::findOrFail($key->tracked_order_id);
            }
            $order=TrackedOrder::create(['customer_id'=>$customer->id,'reference'=>'AT-'.Str::upper(Str::random(12)),'request_path'=>$data['request_path'],'description'=>$data['description'],'stage'=>'inquiry']);
            $this->event($request,$order,'created',$data['description'],true,$admin);
            if($key)DB::table('order_submission_keys')->where('id',$key->id)->update(['tracked_order_id'=>$order->id,'updated_at'=>now()]);
            return $order;
        });
        return $this->json(['order'=>$this->snapshot($order,$admin)],201);
    }
    public function update(Request $request,TrackedOrder $order): JsonResponse {
        $data=$request->validate(['revision'=>['required','string','regex:/^[a-f0-9]{64}$/'],
            'action'=>['required','in:note,stage,stage_correction,invoice_confirmed,deposit_confirmed'],
            'stage'=>['required_if:action,stage,stage_correction',Rule::in(self::STAGES)],'note'=>['required','string','min:3','max:4000'],
            'visible_to_customer'=>['required','boolean']]);
        $order=DB::transaction(function () use($request,$order,$data) {
            $row=TrackedOrder::lockForUpdate()->findOrFail($order->id);
            $this->guardCustomer($request,Customer::findOrFail($row->customer_id));
            abort_unless(hash_equals($this->revision($row),$data['revision']),409);
            $terminal=in_array($row->stage,['cancelled','delivered'],true);
            if($data['action']==='stage_correction'){
                abort_unless(mb_strlen(trim($data['note']))>=10,422);
                if($terminal)abort_unless($request->user()->isPrimaryOwner(),403);
                abort_if($data['stage']===$row->stage,422);
            }elseif($terminal&&$data['action']!=='note')abort(422);
            if ($data['action']==='invoice_confirmed') { abort_if($row->invoice_confirmed_at!==null,422);$row->invoice_confirmed_at=now(); }
            if ($data['action']==='deposit_confirmed') { abort_unless($row->invoice_confirmed_at!==null&&$row->deposit_confirmed_at===null,422);$row->deposit_confirmed_at=now(); }
            if (in_array($data['action'],['stage','stage_correction'],true)) {
                $next=$data['stage'];$current=array_search($row->stage,self::STAGES,true);$target=array_search($next,self::STAGES,true);
                if($data['action']==='stage')abort_unless($next==='cancelled'||abs($target-$current)===1,422);
                if (in_array($next,['production','quality','ready','shipped','delivered'],true)) abort_unless($row->invoice_confirmed_at&&$row->deposit_confirmed_at,422);
                $row->stage=$next;
            }
            $row->save();$this->event($request,$row,$data['action'],$data['note'],$data['visible_to_customer'],true);
            return $row;
        });
        return $this->json(['order'=>$this->snapshot($order,true)]);
    }
    public function commercial(Request $request,TrackedOrder $order): JsonResponse {
        $data=$request->validate(['revision'=>['required','regex:/^[a-f0-9]{64}$/'],'currency'=>['required','in:IRR,IRT,USD,EUR,IQD,AED'],'items'=>['required','array','min:1','max:50'],'items.*'=>['required','array:product_code,description,quantity,unit_price_minor'],'items.*.product_code'=>['nullable','string','exists:products,code'],'items.*.description'=>['required','string','max:300'],'items.*.quantity'=>['required','integer','min:1','max:100000'],'items.*.unit_price_minor'=>['required','integer','min:0','max:1000000000'],'shipping_minor'=>['required','integer','min:0','max:1000000000000'],'tax_minor'=>['required','integer','min:0','max:1000000000000'],'discount_minor'=>['required','integer','min:0','max:1000000000000'],'note'=>['required','string','min:3','max:4000']]);
        $order=DB::transaction(function()use($request,$order,$data){
            $row=$this->locked($request,$order,$data['revision']);abort_if($row->invoice_confirmed_at||in_array($row->stage,['cancelled','delivered'],true),422);
            $subtotal=0;foreach($data['items'] as $item)$subtotal+=(int)$item['quantity']*(int)$item['unit_price_minor'];
            $total=$subtotal+$data['shipping_minor']+$data['tax_minor']-$data['discount_minor'];abort_if($total<0||$total>9000000000000000,422);
            $values=['currency'=>$data['currency'],'items'=>json_encode($data['items'],JSON_THROW_ON_ERROR),'subtotal_minor'=>$subtotal,'total_minor'=>$total,'shipping_minor'=>$data['shipping_minor'],'discount_minor'=>$data['discount_minor'],'tax_minor'=>$data['tax_minor'],'updated_at'=>now()];
            if(DB::table('order_quotes')->where('tracked_order_id',$row->id)->exists())DB::table('order_quotes')->where('tracked_order_id',$row->id)->update($values);
            else DB::table('order_quotes')->insert(array_merge($values,['tracked_order_id'=>$row->id,'created_at'=>now()]));
            $this->event($request,$row,'quote_updated',$data['note'],false,true);return $row;
        });return $this->json(['order'=>$this->snapshot($order,true)]);
    }
    private function locked(Request $request,TrackedOrder $order,string $revision): TrackedOrder {
        $row=TrackedOrder::lockForUpdate()->findOrFail($order->id);$this->guardCustomer($request,Customer::findOrFail($row->customer_id));abort_unless(hash_equals($this->revision($row),$revision),409);return $row;
    }
    public function uploadDocument(Request $request,TrackedOrder $order): JsonResponse {
        $data=$request->validate(['revision'=>['required','regex:/^[a-f0-9]{64}$/'],'kind'=>['required','in:invoice,deposit_receipt'],'visible_to_customer'=>['required','boolean'],'note'=>['required','string','min:3','max:4000'],'document'=>['required','file','mimetypes:application/pdf,image/jpeg,image/png,image/webp','max:8192']]);
        $file=$request->file('document');$mime=$file->getMimeType();$extension=['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;abort_unless($extension,422);
        if($mime==='application/pdf'){abort_unless(file_get_contents($file->getRealPath(),false,null,0,5)==='%PDF-',422);}else{$size=@getimagesize($file->getRealPath());abort_unless($size&&$size[0]<=5000&&$size[1]<=5000,422);$image=@imagecreatefromstring(file_get_contents($file->getRealPath()));abort_unless($image,422);imagedestroy($image);}
        $created=null;
        try{$order=DB::transaction(function()use($request,$order,$data,$file,$extension,&$created){
            $row=$this->locked($request,$order,$data['revision']);abort_if($row->getMedia(TrackedOrder::MEDIA_COLLECTION)->count()>=12,422);
            $created=$row->addMedia($file)->usingFileName(Str::uuid().'.'.$extension)->withCustomProperties(['kind'=>$data['kind'],'visible_to_customer'=>$data['visible_to_customer']])->toMediaCollection(TrackedOrder::MEDIA_COLLECTION);
            $created->setCustomProperty('sha256',hash_file('sha256',$created->getPath()))->save();
            $this->event($request,$row,'document_added',$data['note'],$data['visible_to_customer'],true);return $row;
        });}catch(\Throwable $e){if($created)$created->delete();throw $e;}
        return $this->json(['order'=>$this->snapshot($order,true)],201);
    }
    public function downloadDocument(Request $request,TrackedOrder $order,int $document) {
        $admin=$this->admin($request);
        if($admin)$this->guardCustomer($request,Customer::findOrFail($order->customer_id));else abort_unless($order->customer_id===$request->attributes->get('armaghan.customer')->id,404);
        $media=$order->media()->where('collection_name',TrackedOrder::MEDIA_COLLECTION)->findOrFail($document);abort_unless($admin||$media->getCustomProperty('visible_to_customer',false),404);
        return response()->download($media->getPath(),$order->reference.'-'.$media->getCustomProperty('kind').'.'.pathinfo($media->file_name,PATHINFO_EXTENSION),['Content-Type'=>$media->mime_type,'X-Content-Type-Options'=>'nosniff','Cache-Control'=>'no-store, private']);
    }
    private function guardCustomer(Request $request,Customer $customer): void {
        if (strtolower((string)$customer->user?->email)===config('owner-access.primary_owner_email')) abort_unless($request->user()->isPrimaryOwner()&&$request->user()->id===$customer->user_id,403);
    }
    private function revision(TrackedOrder $order): string {
        return hash('sha256',json_encode([$order->attributesToArray(),$order->events()->orderBy('id')->get()->toArray(),DB::table('order_quotes')->where('tracked_order_id',$order->id)->first(),$order->media()->orderBy('id')->get(['id','custom_properties','updated_at'])->map(fn($m)=>$m->only(['id','custom_properties','updated_at']))->all()],JSON_THROW_ON_ERROR));
    }
    private function snapshot(TrackedOrder $order,bool $admin): array {
        $order->refresh(); // Fingerprint persisted defaults/timestamps, never transient create/save attributes.
        $events=$order->events()->orderBy('id');if (!$admin) $events->where('visible_to_customer',true);
        $data=$order->only(['id','reference','request_path','description','stage','invoice_confirmed_at','deposit_confirmed_at','created_at']);
        $quote=DB::table('order_quotes')->where('tracked_order_id',$order->id)->first();
        $data['quote']=$quote&&($admin||$order->invoice_confirmed_at)?array_merge((array)$quote,['items'=>json_decode($quote->items,true)]):null;
        if($data['quote'])unset($data['quote']['id'],$data['quote']['tracked_order_id']);
        $data['documents']=$order->getMedia(TrackedOrder::MEDIA_COLLECTION)->filter(fn($m)=>$admin||$m->getCustomProperty('visible_to_customer',false))->map(fn($m)=>['id'=>$m->id,'kind'=>$m->getCustomProperty('kind'),'visible_to_customer'=>$m->getCustomProperty('visible_to_customer',false),'download_url'=>'/backend/api/'.($admin?'admin':'customer').'/orders/'.$order->id.'/documents/'.$m->id])->values()->all();
        $data['events']=$events->get()->map(fn($e)=>$e->only(['id','action','stage','note','visible_to_customer','created_at']))->all();
        if ($admin) { $data['customer_id']=$order->customer_id; $customer=Customer::with('user')->findOrFail($order->customer_id); $data['customer_name']=$customer->company_name ?: $customer->user?->name; $data['revision']=$this->revision($order); }
        return $data;
    }
    private function event(Request $request,TrackedOrder $order,string $action,string $note,bool $visible,bool $admin): void {
        $order->events()->create(['actor_user_id'=>$admin?$request->user()->id:null,'action'=>$action,'stage'=>$order->stage,'note'=>$note,'visible_to_customer'=>$visible]);
        ActivityLog::create(['actor_user_id'=>$admin?$request->user()->id:null,'customer_id'=>$order->customer_id,'action'=>'order.'.$action,'subject_type'=>TrackedOrder::class,'subject_id'=>$order->id,'metadata'=>['stage'=>$order->stage]]);
    }
    private function json(array $data,int $status=200): JsonResponse { return response()->json($data,$status)->header('Cache-Control','no-store, private'); }
}
