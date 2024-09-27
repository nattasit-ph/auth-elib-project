<?php

namespace App\Http\Controllers\Back\Room;

use DB;
use Auth;
use Session;
use App\Http\Controllers\Back\BackController;
use App\Models\Room;
use App\Models\RoomGallery;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;

class RoomSettingController extends BackController
{
	public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
			parent::getSiteConfig();
 			
        	$this->user = Auth::user();
    		if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
    			return $next($request);
 			}
 			else {
 				return redirect()->route('home');
 			}
		});
 	}

    public function index()
    {
        $user_groups = UserGroup::active()->myOrg()->get();
        // dd($user_groups[0]->data_rooms['in_advance_day']);   
        return view('back.'.config('bookdose.theme_back').'.modules.room.setting.form', compact('user_groups'));
    }

    public function update(Request $request)
    {

		// data_rooms (setting);
		$user_groups = UserGroup::active()->myOrg()->get();

        // dd($request, $user_groups);
    	$arr = [];
    	foreach($user_groups as $user_group) {
    		$arr = [];
    		$arr['in_advance_day'] = $request->{$user_group->id.'_in_advance_day'} ?? '';
    		$arr['per_day'] = $request->{$user_group->id.'_per_day'} ?? '';
    		$arr['max_hour'] = $request->{$user_group->id.'_max_hour'} ?? '';
            $validatedData['data_rooms'] = $arr;
            UserGroup::where('id', $user_group->id)->update($validatedData);
    	}

        //--- Start log ---//
 		$log = collect([ (object)[
            'module' => 'RoomSetting', 
            'severity' => 'Info', 
            'title' => 'Update', 
            'desc' => '[Succeeded] - Update data_rooms from user_groups ',
         ]])->first();
        parent::Log($log);
        //--- End log ---//
        return redirect()->route('admin.room.setting.all')->with('success', 'Room Setting is successfully updated.');

        dd($validatedData);
		
    }
}
