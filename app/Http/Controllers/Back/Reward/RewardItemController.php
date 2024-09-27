<?php

namespace App\Http\Controllers\Back\Reward;

use DB;
use Auth;
use App\Models\RewardItem;
use App\Models\RewardItemGallery;
use App\Models\RewardCategory;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class RewardItemController extends BackController
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
        $org_slug = Auth::user()->org->slug;

		return view('back.'.config('bookdose.theme_back').'.modules.reward.reward_item.list', compact('org_slug'));
	}

	public function create()
 	{
        $org_slug = Auth::user()->org->slug;

		$reward_categories = RewardCategory::active()->orderBy('title', 'ASC')->get();
     	return view('back.'.config('bookdose.theme_back').'.modules.reward.reward_item.form', compact('org_slug', 'reward_categories'));
 	}

	public function edit(Request $request, $org_slug, $id)
 	{
        $org_slug = Auth::user()->org->slug;

     	$page_header = 'Edit reward';
 		$reward_item = RewardItem::findOrFail($id);
		$reward_categories = RewardCategory::active()->orderBy('title', 'ASC')->get();
     	return view('back.'.config('bookdose.theme_back').'.modules.reward.reward_item.form', compact('org_slug', 'page_header', 'reward_item', 'reward_categories'));
 	}

 	public function store(Request $request)
 	{
        $org_slug = Auth::user()->org->slug;

 	 	$validatedData = $request->validate([
         'title' => 'required|max:255',
         'description' => 'required',
         'reward_category_id' => 'required',
         'point' => 'required|integer|gte:0',
         'max_per_user' => 'nullable|integer|gt:0',
         'stock_avail' => 'required|integer|gt:0',
         'started_at' => 'nullable|date',
         'expired_at' => 'nullable|date',
         'status' => 'boolean',
     	],
     	[
     		'title.required' => 'Please specify reward name.',
     		'description.required' => 'Please specify description.',
     		'reward_category_id.required' => 'Please choose category.',
     		'point.required' => 'Please specify redeem coins.',
			'point.gte' => 'Coins must be equal or greater than 0.',
			'max_per_user.gt' => 'Max item per user must be equal or greater than 0.',
			'stock_avail.required' => 'Please specify available stock.',
			'stock_avail.gt' => 'Available stock must be greater than 0.',
     	]);
     	$validatedData['created_by'] = Auth::user()->id;
		$validatedData['updated_by'] = Auth::user()->id;
		$data = $validatedData;
     	$item = RewardItem::create($data);
     	//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Reward',
      		'severity' => 'Info',
      		'title' => 'Insert',
      		'desc' => '[Succeeded] - '.$item->title,
   		]])->first();
  		parent::Log($log, Auth::user());
  		//--- End log ---//
     	return redirect(route('admin.reward.edit', [$org_slug, $item->id]))->with('success', request()->title.' is successfully saved.');
 	}

 	public function update(Request $request, $org_slug, $id)
 	{
        $org_slug = Auth::user()->org->slug;

 	 	$validatedData = $request->validate([
         'title' => 'required|max:255',
         'description' => 'required',
         'reward_category_id' => 'required',
         'point' => 'required|integer|gte:0',
         'max_per_user' => 'nullable|integer|gt:0',
         'stock_avail' => 'required|integer|gt:0',
         'started_at' => 'nullable|date',
         'expired_at' => 'nullable|date',
         'status' => 'boolean',
     	],
     	[
     		'title.required' => 'Please specify reward name.',
     		'description.required' => 'Please specify description.',
     		'reward_category_id.required' => 'Please choose category.',
     		'point.required' => 'Please specify redeem coins.',
			'point.gte' => 'Coins must be equal or greater than 0.',
			'max_per_user.gt' => 'Max item per user must be equal or greater than 0.',
			'stock_avail.required' => 'Please specify available stock.',
			'stock_avail.gt' => 'Available stock must be greater than 0.',
     	]);
		$validatedData['updated_by'] = Auth::user()->id;
		$data = $validatedData;
     	$item = RewardItem::where('id', $id)->update($data);
     	//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Reward',
      		'severity' => 'Info',
      		'title' => 'Update',
      		'desc' => '[Succeeded] - '.$validatedData['title'],
   		]])->first();
  		parent::Log($log, Auth::user());
  		//--- End log ---//
     	return redirect()->route('admin.reward.edit', [$org_slug, $id])->with('success', request()->title.' is successfully saved.');
 	}

	public function ajaxGetData(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$query = RewardItem::select(
    			array(
    				'*',
    				DB::raw('DATE_FORMAT(reward_items.created_at, "%d/%m/%Y") AS created_date'),
	      		DB::raw('DATE_FORMAT(reward_items.updated_at, "%d/%m/%Y") AS updated_date'),
    			)
    		);

		$reward_category_id = $request->reward_category_id ?? '';
		if ($reward_category_id > 0) {
			$query = $query->where('reward_items.reward_category_id', $reward_category_id);
		}

    	$datatable = new Datatables;
    	return $datatable
	    		->eloquent($query)
	         ->addColumn('title_action', function ($reward_item) use ($org_slug) {
	            	return '<a href="'.route('admin.reward.edit', [$org_slug, $reward_item->id]).'" class="">'.$reward_item->title.'</a>';
	         })
	         ->addColumn('expired_date', function ($reward_item) {
	             return empty($reward_item->expired_at) ? '' : date('d/m/Y', strtotime($reward_item->expired_at));
	         })
	         ->addColumn('status_html', function ($reward_item) {
	         	if ($reward_item->status == 1)
	             	return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
	            else
	            	return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
	         })
	         ->addColumn('actions', function ($reward_item) {
	         	$menu_status = '';
	         	$menu_delete = '';
	         	// if (Auth::user()->hasAnyPermission(['reward.create'])) {
	         	// 	$menu_duplicate = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reward_item->id).' data-title='.json_encode($reward_item->title, JSON_UNESCAPED_UNICODE).' onClick="duplicateItem(this)"><i class="la la-copy"></i> Duplicate</a>';
	         	// }

         		if ($reward_item->status == 1) {
         			$menu_status = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reward_item->id).' data-status='.json_encode($reward_item->status).' data-title='.json_encode($reward_item->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>';
         		}
         		else {
         			$menu_status = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reward_item->id).' data-status='.json_encode($reward_item->status).' data-title='.json_encode($reward_item->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>';
         		}
         		$menu_delete = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reward_item->id).' data-title='.json_encode($reward_item->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>';

	         	if (!empty($menu_status) || !empty($menu_delete)) {
	         		return '
				         	<span class="dropdown">
		                      <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
		                        <i class="la la-ellipsis-h"></i>
		                      </a>
		                      <div class="dropdown-menu dropdown-menu-right">
		                          	'.$menu_status.'
		                          	'.$menu_delete.'
		                      </div>
		                  </span>';
	         	}
         		return '';
	         })
	         ->rawColumns(['title_action', 'expired_date', 'status_html', 'actions'])
	         ->addIndexColumn()
	    		->make(true);
 	}

	public function setStatus(Request $request)
	{
    	$id = $request->input('id');
    	$item = RewardItem::find($id);
 		if ($item) {
    		$update_data = array(
    			'status' => ($request->input('status') == '1' ? '0' : '1'),
    			'updated_at' => now(),
    			'updated_by' => Auth::user()->id,
    		);
        	$rs = RewardItem::where('id', $id)->update($update_data);
        	if ($rs) {
        		//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'Reward',
		      		'severity' => 'Info',
		      		'title' => 'Update status',
		      		'desc' => '[Succeeded] - '.$item->title,
		   		]])->first();
		  		parent::Log($log, Auth::user());
		  		//--- End log ---//

        		return json_encode(array(
				    'status' => 200,
				    'notify_title' => 'Hooray!',
				    'notify_msg' => 'Status has been updated successfully.',
	 				 'notify_icon' => 'icon la la-check-circle',
	 				 'notify_type' => 'success',
				));
        	}
     }
     return json_encode(array(
		   'status' => 500,
			'notify_title' => 'Oops!',
			'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
			'notify_icon' => 'icon la la-warning',
			'notify_type' => 'danger',
		));
 	}

 	public function destroy(Request $request)
 	{
     	$id = $request->input('id');
  		$item = RewardItem::where('id', $id)->first();
  		$rs = RewardItem::where('id', $id)->delete();
  		if ($rs) {
  			RewardItemGallery::where('reward_item_id', $id)->delete();
  			Storage::deleteDirectory('rewards/'.$id);
  			//--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'Reward',
	      		'severity' => 'Info',
	      		'title' => 'Delete',
	      		'desc' => '[Succeeded] - '.$item->title.' (id = '.$id.')'
	   		]])->first();
	  		parent::Log($log, Auth::user());
	  		//--- End log ---//

     		return json_encode(array(
			    'status' => 200,
			    'notify_title' => 'Hooray!',
			    'notify_msg' => $item->title.' has been deleted successfully.',
 				 'notify_icon' => 'icon la la-check-circle',
 				 'notify_type' => 'success',
			));
     	}

     	return json_encode(array(
		   'status' => 500,
			'notify_title' => 'Oops!',
			'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
			'notify_icon' => 'icon la la-warning',
			'notify_type' => 'danger',
		));
 	}

}
