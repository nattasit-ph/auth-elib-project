<?php

namespace App\Http\Controllers\Back\Reward;
use DB;
use Auth;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use App\Models\RewardActivity;
use Illuminate\Support\Facades\Validator;

class RewardActivityController extends BackController
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

 	public function edit()
 	{
		$activities = RewardActivity::active()
			->orderBy('weight', 'asc')
			->orderBy('title', 'asc')
			->get();

     	return view('back.'.config('bookdose.theme_back').'.modules.reward.reward_activity.form', compact('activities'));
 	}

	public function update(Request $request)
 	{

		//check group collect & test & share
		$validatedDataCollect = Validator::make($request->all(), [
			'collect_id' => 'required',
			'collect_point.*' => 'required|integer|min:0',
		])->validate();
	
		// update group collect & test & share
		foreach( $request->collect_id as $index => $item_id ) {

			$item_point = $request->collect_point[$index];

			$group_collect = RewardActivity::where('id', $item_id)
              ->update(['point' => $item_point, 'updated_by' => Auth::user()->id]);
		}
		
		return redirect(route('admin.coin-activity.edit'))->with('success','Coin Activity is successfully updated.');
 	}

}
