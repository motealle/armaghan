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
            'request_path'=>['required',Rule::in(self::PATHS)],'description'=>['required','string','min:3','max:4000']]);
        $customer=$admin?Customer::findOrFail($data['customer_id']):$request->attributes->get('armaghan.customer');
        if ($admin) $this->guardCustomer($request,$customer);
        abort_unless($customer->active,422);
        $order=DB::transaction(function () use($request,$data,$customer,$admin) {
            $order=TrackedOrder::create(['customer_id'=>$customer->id,'reference'=>'AT-'.Str::upper(Str::random(12)),'request_path'=>$data['request_path'],'description'=>$data['description'],'stage'=>'inquiry']);
            $this->event($request,$order,'created',$data['description'],true,$admin);
            return $order;
        });
        return $this->json(['order'=>$this->snapshot($order,$admin)],201);
    }
    public function update(Request $request,TrackedOrder $order): JsonResponse {
        $data=$request->validate(['revision'=>['required','string','regex:/^[a-f0-9]{64}$/'],
            'action'=>['required','in:note,stage,invoice_confirmed,deposit_confirmed'],
            'stage'=>['required_if:action,stage',Rule::in(self::STAGES)],'note'=>['required','string','min:3','max:4000'],
            'visible_to_customer'=>['required','boolean']]);
        $order=DB::transaction(function () use($request,$order,$data) {
            $row=TrackedOrder::lockForUpdate()->findOrFail($order->id);
            $this->guardCustomer($request,Customer::findOrFail($row->customer_id));
            abort_unless(hash_equals($this->revision($row),$data['revision']),409);
            abort_if(in_array($row->stage,['cancelled','delivered'],true)&&$data['action']!=='note',422);
            if ($data['action']==='invoice_confirmed') { abort_if($row->invoice_confirmed_at!==null,422);$row->invoice_confirmed_at=now(); }
            if ($data['action']==='deposit_confirmed') { abort_unless($row->invoice_confirmed_at!==null&&$row->deposit_confirmed_at===null,422);$row->deposit_confirmed_at=now(); }
            if ($data['action']==='stage') {
                $next=$data['stage'];$current=array_search($row->stage,self::STAGES,true);$target=array_search($next,self::STAGES,true);
                abort_unless($next==='cancelled'||abs($target-$current)===1,422);
                if (in_array($next,['production','quality','ready','shipped','delivered'],true)) abort_unless($row->invoice_confirmed_at&&$row->deposit_confirmed_at,422);
                $row->stage=$next;
            }
            $row->save();$this->event($request,$row,$data['action'],$data['note'],$data['visible_to_customer'],true);
            return $row;
        });
        return $this->json(['order'=>$this->snapshot($order,true)]);
    }
    private function guardCustomer(Request $request,Customer $customer): void {
        if (strtolower((string)$customer->user?->email)===config('owner-access.primary_owner_email')) abort_unless($request->user()->isPrimaryOwner()&&$request->user()->id===$customer->user_id,403);
    }
    private function revision(TrackedOrder $order): string {
        return hash('sha256',json_encode([$order->attributesToArray(),$order->events()->orderBy('id')->get()->toArray()],JSON_THROW_ON_ERROR));
    }
    private function snapshot(TrackedOrder $order,bool $admin): array {
        $order->refresh(); // Fingerprint persisted defaults/timestamps, never transient create/save attributes.
        $events=$order->events()->orderBy('id');if (!$admin) $events->where('visible_to_customer',true);
        $data=$order->only(['id','reference','request_path','description','stage','invoice_confirmed_at','deposit_confirmed_at','created_at']);
        $data['events']=$events->get()->map(fn($e)=>$e->only(['id','action','stage','note','visible_to_customer','created_at']))->all();
        if ($admin) { $data['customer_id']=$order->customer_id;$data['revision']=$this->revision($order); }
        return $data;
    }
    private function event(Request $request,TrackedOrder $order,string $action,string $note,bool $visible,bool $admin): void {
        $order->events()->create(['actor_user_id'=>$admin?$request->user()->id:null,'action'=>$action,'stage'=>$order->stage,'note'=>$note,'visible_to_customer'=>$visible]);
        ActivityLog::create(['actor_user_id'=>$admin?$request->user()->id:null,'customer_id'=>$order->customer_id,'action'=>'order.'.$action,'subject_type'=>TrackedOrder::class,'subject_id'=>$order->id,'metadata'=>['stage'=>$order->stage]]);
    }
    private function json(array $data,int $status=200): JsonResponse { return response()->json($data,$status)->header('Cache-Control','no-store, private'); }
}
