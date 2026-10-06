<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\StyleProfile;
use App\Services\StyleProfileService;
use App\Services\Media\ProductMediaVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class HomeMediaController extends Controller {
 public const TARGETS=['hero','about','capability.production','capability.export','capability.trade','banner.1','banner.2','banner.3'];
 public function __construct(private StyleProfileService $profiles) {}
 private function json(array $data,int $status=200) {return response()->json($data,$status)->header('Cache-Control','no-store, private');}
 private function revision(StyleProfile $profile): string {return hash('sha256',json_encode($profile->media()->where('collection_name',StyleProfile::MEDIA_COLLECTION)->orderBy('id')->get(['id','custom_properties','updated_at'])->map(fn($m)=>$m->only(['id','custom_properties','updated_at']))->all(),JSON_THROW_ON_ERROR));}
 private function snapshot(StyleProfile $profile): array {return ['revision'=>$this->revision($profile),'images'=>$profile->getMedia(StyleProfile::MEDIA_COLLECTION)->map(fn($m)=>['id'=>$m->id,'target'=>$m->getCustomProperty('target'),'url'=>route('admin.home-media.file',['media'=>$m->id,'variant'=>'thumb']),'channels'=>$m->getCustomProperty('channels',[])])->values()->all()];}
 public function index(Request $request) {return $this->json($this->snapshot($this->profiles->ensureDefault($request->user())));}
 public function publicIndex(string $channel) {
  $profile=$this->profiles->findDefault();$images=[];$srcsets=[];
  if($profile)foreach($profile->getMedia(StyleProfile::MEDIA_COLLECTION) as $m){
   if(!in_array($channel,$m->getCustomProperty('channels',[]),true))continue;
   $target=$m->getCustomProperty('target');
   $variant=$target==='hero'||str_starts_with($target,'banner.')?'detail':'card';
   $images[$target]=route('home-media.file',['media'=>$m->id,'variant'=>$variant]);
   $size=@getimagesize($m->getPath());$sourceWidth=is_array($size)?(int)$size[0]:0;
   $candidates=[];
   foreach(['thumb'=>320,'card'=>800,'detail'=>1600] as $name=>$maximum){
    $width=min($maximum,$sourceWidth);
    if($width>0&&!isset($candidates[$width]))$candidates[$width]=route('home-media.file',['media'=>$m->id,'variant'=>$name]).' '.$width.'w';
   }
   if($candidates)$srcsets[$target]=implode(', ',array_values($candidates));
  }
  return $this->json(['images'=>(object)$images,'srcsets'=>(object)$srcsets]);
 }
 public function adminFile(Media $media,string $variant='original') {return $this->mediaFile($media,$variant,true);}
 public function publicFile(Media $media,string $variant='original') {
  abort_unless(count($media->getCustomProperty('channels',[]))>0,404);
  return $this->mediaFile($media,$variant,false);
 }
 private function mediaFile(Media $media,string $variant,bool $private) {
  abort_unless($media->model_type===StyleProfile::class&&$media->collection_name===StyleProfile::MEDIA_COLLECTION,404);
  abort_unless(in_array($variant,['original','thumb','card','detail'],true),404);
  // Derivative writes are delivery housekeeping and must not invalidate an open editor revision.
  $derived=$variant==='original'?null:app(ProductMediaVariant::class)->path($media,$variant,false);
  $path=$derived??$media->getPath();abort_unless(is_file($path)&&filesize($path)>0,404);
  $cache=$private?'private, no-store':($derived||$variant==='original'?'public, max-age=31536000, immutable':'public, max-age=60');
  return response()->file($path,['Cache-Control'=>$cache,'Content-Type'=>$derived?'image/webp':(string)$media->mime_type,'X-Content-Type-Options'=>'nosniff']);
 }
 public function upload(Request $request) {
  $data=$request->validate(['revision'=>['required','regex:/^[a-f0-9]{64}$/'],'target'=>['required',Rule::in(self::TARGETS)],'image'=>['required','file','image','extensions:jpg,jpeg,png,webp','mimetypes:image/jpeg,image/png,image/webp','max:8192','dimensions:max_width=5000,max_height=5000']]);
  $file=$request->file('image');$extension=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$file->getMimeType()]??null;
  $decoded=$extension?@imagecreatefromstring(file_get_contents($file->getRealPath())):false;abort_unless($decoded,422);imagedestroy($decoded);
  $profile=$this->profiles->ensureDefault($request->user());$created=null;
  try {DB::transaction(function() use($request,$profile,$data,$file,$extension,&$created){
   $profile=StyleProfile::lockForUpdate()->findOrFail($profile->id);abort_unless(hash_equals($this->revision($profile),$data['revision']),409);
   abort_if($profile->media()->where('collection_name',StyleProfile::MEDIA_COLLECTION)->count()>=64,422,'Home image history limit reached.');
   $created=$profile->addMedia($file)->usingFileName(Str::uuid().'.'.$extension)->withCustomProperties(['target'=>$data['target'],'channels'=>[]])->toMediaCollection(StyleProfile::MEDIA_COLLECTION);
   $this->audit($request,$profile,'home.image-uploaded',['target'=>$data['target'],'media_id'=>$created->id]);
  });}catch(\Throwable $e){if($created)$created->delete();throw $e;}
  return $this->json($this->snapshot($profile->fresh()),201);
 }
 public function publish(Request $request,string $channel) {
  $data=$request->validate(['revision'=>['required','regex:/^[a-f0-9]{64}$/'],'selection'=>['present','array','max:8'],'selection.*'=>['required','integer','distinct']]);
  $profile=$this->profiles->ensureDefault($request->user());
  DB::transaction(function()use($request,$profile,$data,$channel){
   $profile=StyleProfile::lockForUpdate()->findOrFail($profile->id);abort_unless(hash_equals($this->revision($profile),$data['revision']),409);
   $media=$profile->getMedia(StyleProfile::MEDIA_COLLECTION)->keyBy('id');$targets=[];
   foreach($data['selection'] as $id){abort_unless(isset($media[$id]),422);$target=$media[$id]->getCustomProperty('target');abort_unless(in_array($target,self::TARGETS,true)&&!in_array($target,$targets,true),422);$targets[]=$target;}
   foreach($media as $m){$channels=array_values(array_diff($m->getCustomProperty('channels',[]),[$channel]));if(in_array($m->id,$data['selection'],true))$channels[]=$channel;$m->setCustomProperty('channels',$channels)->save();}
   $this->audit($request,$profile,'home.images-published',['channel'=>$channel,'media_ids'=>$data['selection']]);
  });return $this->json($this->snapshot($profile->fresh()));
 }
 private function audit(Request $request,StyleProfile $profile,string $action,array $metadata): void {ActivityLog::create(['actor_user_id'=>$request->user()->id,'action'=>$action,'subject_type'=>StyleProfile::class,'subject_id'=>$profile->id,'metadata'=>$metadata]);}
}
