<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
class AdvancedAccessController extends Controller
{
    public function store(Request $request)
    {
        abort_unless($request->user()->isPrimaryOwner(),403);
        $data=$request->validate(['reason'=>['required','in:integration-gap,diagnosis,recovery']]);
        $request->session()->put('armaghan.advanced_admin.user_id',$request->user()->id);
        $request->session()->put('armaghan.advanced_admin.until',now()->addMinutes(15)->timestamp);
        ActivityLog::create(['actor_user_id'=>$request->user()->id,'action'=>'admin.advanced.opened','metadata'=>['reason'=>$data['reason']]]);
        return response()->json(['redirect'=>'/backend/admin','expires_in'=>900])->header('Cache-Control','no-store, private');
    }
    public function destroy(Request $request)
    {
        $request->session()->forget(['armaghan.advanced_admin.user_id','armaghan.advanced_admin.until']);
        return response()->json(['ok'=>true])->header('Cache-Control','no-store, private');
    }
}
