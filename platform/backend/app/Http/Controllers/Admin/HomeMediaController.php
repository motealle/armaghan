<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\StyleProfile;
use App\Services\StyleProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class HomeMediaController extends Controller {
 public const TARGETS=['hero','about','capability.production','capability.export','capability.trade','banner.1','banner.2','banner.3'];
 public function __construct(private StyleProfileService $profiles) {}
 private function json(array $data,int $status=200) {return response()->json($data,$status)->header('Cache-Control','no-store, private');}
 private function revision(StyleProfile $profile): string {return hash('sha256',json_encode($profile->media()->where('collection_name',StyleProfile::MEDIA_COLLECTION)->orderBy('id')->get(['id','custom_properties','updated_at'])->toArray(),JSON_THROW_ON_ERROR));}
 private function snapshot(StyleProfile $profile): array {return ['revision'=>$this->revision($profile),'images'=>$profile->getMedia(StyleProfile::MEDIA_COLLECTION)->map(fn($m)=>['id'=>$m->id,'target'=>$m->getCustomProperty('target'),'url'=>$m->getUrl(),'channels'=>$m->getCustomProperty('channels',[])])->values()->all()];}
 public function index(Request $request) {return $this->json($this->snapshot($this->profiles->ensureDefault($request->user())));}
 public function publicIndex(string $channel) {
  $profile=$this->profiles->findDefault();$images=[];
  if($profile)foreach($profile->getMedia(StyleProfile::MEDIA_COLLECTION) as $m)if(in_array($channel,$m->getCustomProperty('channels',[]),true))$images[$m->getCustomProperty('target')]=$m->getUrl();
  return $this->json(['images'=>(object)$images]);
 }
 public function upload(Request $request) {
  $data=$request->validate(['revision'=>['required','regex:/^[a-f0-9]{64}$/'],'target'=>['required',Rule::in(self::TARGETS)],'image'=>['required','file','image','mimetypes:image/jpeg,image/png,image/webp','max:8192','dimensions:max_width=5000,max_height=5000']]);
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
