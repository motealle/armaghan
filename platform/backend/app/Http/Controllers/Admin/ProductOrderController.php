<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Product,ActivityLog};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{Request,JsonResponse};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\{Rule,ValidationException};
class ProductOrderController extends Controller {
 private function query(string $group): Builder {
  $query=Product::query()->where('active',true)->whereHas('subcategory',fn($s)=>$s->where('active',true)->whereHas('category',fn($c)=>$c->where('active',true)));
  return $group==='available'?$query->where('availability','available'):$query->whereIn('availability',['made_to_order','unavailable']);
 }
 private function revision($rows): string {
  return hash('sha256',json_encode($rows->sortBy('id')->values()->map(fn($p)=>$p->only(['id','code','subcategory_id','availability','sort_order','updated_at']))->all(),JSON_THROW_ON_ERROR));
 }
 private function snapshot(): array {
  $groups=[];
  foreach(['available','producible'] as $group){
   $rows=$this->query($group)->with(['subcategory','media'])->orderBy('sort_order')->orderBy('code')->get();
   abort_if($rows->count()>5000,413);
   $groups[$group]=['revision'=>$this->revision($rows),'products'=>$rows->map(function($p){
    $media=$p->media->where('collection_name',Product::MEDIA_COLLECTION)->sortBy('order_column')->first();
    return ['id'=>$p->id,'code'=>$p->code,'subcategory_code'=>$p->subcategory->code,'label'=>$p->subcategory->name_fa,'sort_order'=>$p->sort_order,
     'thumb_url'=>$media?route('catalog.product-media',['media'=>$media->id,'variant'=>'thumb']):null];
   })->values()->all()];
  }
  return ['groups'=>$groups];
 }
 public function index(): JsonResponse {return response()->json($this->snapshot())->header('Cache-Control','no-store, private');}
 public function update(Request $request): JsonResponse {
  $data=$request->validate(['group'=>['required',Rule::in(['available','producible'])],
   'revision'=>['required','string','regex:/^[a-f0-9]{64}$/'],
   'ids'=>['present','array','max:5000'],'ids.*'=>['required','integer','distinct']]);
  DB::transaction(function() use($data,$request){
   $rows=$this->query($data['group'])->orderBy('id')->lockForUpdate()->get();
   abort_unless(hash_equals($this->revision($rows),$data['revision']),409,'Product order changed; reload.');
   $ids=array_map('intval',$data['ids']);$expected=$rows->modelKeys();$submitted=$ids;sort($expected);sort($submitted);
   if($submitted!==$expected)throw ValidationException::withMessages(['ids'=>'Order must contain exactly the active products in this group.']);
   $owned=$rows->keyBy('id');
   foreach($ids as $index=>$id)if($owned[$id]->sort_order!==$index)$owned[$id]->update(['sort_order'=>$index]);
   ActivityLog::create(['actor_user_id'=>$request->user()->id,'action'=>'admin.product.order-updated','subject_type'=>Product::class,'subject_id'=>null,'metadata'=>['group'=>$data['group'],'count'=>count($ids)]]);
  });
  return $this->index();
 }
}
