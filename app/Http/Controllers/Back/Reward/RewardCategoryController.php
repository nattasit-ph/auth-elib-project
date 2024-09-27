<?php

namespace App\Http\Controllers\Back\Reward;

use DB;
use Auth;
use App\Models\RewardItem;
use App\Models\RewardCategory;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RewardCategoryController extends BackController
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

		return view('back.'.config('bookdose.theme_back').'.modules.reward.reward_category.list', compact('org_slug'));
	}

	public function create()
 	{
        $org_slug = Auth::user()->org->slug;
     	return view('back.'.config('bookdose.theme_back').'.modules.reward.reward_category.form', compact('org_slug'));
 	}

 	public function edit(Request $request, $org_slug, $id)
 	{
        $org_slug = Auth::user()->org->slug;
     	$reward_category = RewardCategory::findOrFail($id);
     	$page_header = 'Edit reward category';
     	return view('back.'.config('bookdose.theme_back').'.modules.reward.reward_category.form', compact('org_slug', 'reward_category', 'page_header'));
 	}

 	public function store(Request $request)
 	{
        $org_slug = Auth::user()->org->slug;

 	 	$validatedData = $request->validate([
         'title' => 'required|max:255',
         'slug' => 'nullable|alpha_dash|max:255',
         'status' => 'boolean',
     	]);
		if(empty($validatedData['slug'])){
			$validatedData['slug'] = md5(uniqid(rand(), true));
		}
     	$data = $validatedData;
     	$data['created_by'] = Auth::user()->id;
		$data['updated_by'] = Auth::user()->id;
     	$item = RewardCategory::create($data);
     	if ($item) {
     		//--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'Reward Category',
	      		'severity' => 'Info',
	      		'title' => 'Insert',
	      		'desc' => '[Succeeded] - '.$item->title,
	   		]])->first();
	  		parent::Log($log, Auth::user());
	  		//--- End log ---//
     	}
     	if ($request->save_option == 1)
     		return redirect(route('admin.reward-category.edit', [$org_slug, $item->id]))->with('success', request()->title.' is successfully saved.');
     	else
     		return redirect(route('admin.reward-category.create', $org_slug))->with('success', request()->title.' is successfully saved.');
 	}

 	public function update(Request $request, $id)
 	{
        $org_slug = Auth::user()->org->slug;

 		$validatedData = $request->validate([
         'title' => 'required|max:255',
         'slug' => 'required|alpha_dash|max:255',
         'status' => 'boolean',
     	]);
     	$data = $validatedData;
		$data['updated_by'] = Auth::user()->id;
     	RewardCategory::where('id', $id)->update($data);
     	//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Reward Category',
      		'severity' => 'Info',
      		'title' => 'Update',
      		'desc' => '[Succeeded] - '.$validatedData['title'],
   		]])->first();
  		parent::Log($log, Auth::user());
  		//--- End log ---//
     	return redirect(route('admin.reward-category.edit', [$org_slug, $id]))->with('success', request()->title.' is successfully updated.');
   }

	public function ajaxGetData(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$query = RewardCategory::withCount(['rewardItems' => function (Builder $query) {
				$query->where('status', 1);
			}]);

    	$datatable = new Datatables;
    	return $datatable
	    		->eloquent($query)
	         ->addColumn('title_action', function ($reward_item) use ($org_slug) {
	            	return '<a href="'.route('admin.reward-category.edit', [$org_slug, $reward_item->id]).'" class="">'.$reward_item->title.'</a>';
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

         		// if ($reward_item->status == 1) {
         		// 	$menu_status = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reward_item->id).' data-status='.json_encode($reward_item->status).' data-title='.json_encode($reward_item->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>';
         		// }
         		// else {
         		// 	$menu_status = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reward_item->id).' data-status='.json_encode($reward_item->status).' data-title='.json_encode($reward_item->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>';
         		// }
         		$menu_delete = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reward_item->id).' data-title='.json_encode($reward_item->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>';

	         	if (!empty($menu_status) || !empty($menu_delete)) {
	         		return '
				         	<span class="dropdown">
		                      <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
		                        <i class="la la-ellipsis-h"></i>
		                      </a>
		                      <div class="dropdown-menu dropdown-menu-right">
		                          	'.$menu_delete.'
		                      </div>
		                  </span>';
	         	}
         		return '';
	         })
	         ->rawColumns(['title_action', 'status_html', 'actions'])
	         ->addIndexColumn()
	    		->make(true);
 	}

	public function setStatus(Request $request)
	{
    	$id = $request->input('id');
    	$item = RewardCategory::find($id);
 		if ($item) {
 			$new_status = $request->input('status') == '1' ? '0' : '1';
    		$update_data = array(
    			'status' => $new_status,
    			'updated_at' => now(),
    			'updated_by' => Auth::user()->id,
    		);
        	$rs = RewardCategory::where('id', $id)->update($update_data);
        	if ($rs) {
        		if ($new_status == '0') {
        			RewardItem::where('reward_category_id', $id)->update([ 'status' => '0' ]);
        		}
        		//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'Reward Category',
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
  		$item = RewardCategory::where('id', $id)->first();
  		$rs = RewardCategory::where('id', $id)->delete();
  		if ($rs) {
  			//--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'Reward Category',
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
