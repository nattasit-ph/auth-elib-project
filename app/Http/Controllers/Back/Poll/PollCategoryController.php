<?php

namespace App\Http\Controllers\Back\Poll;

use DB;
use Auth;
use Session;
use App\Models\Poll;
use App\Models\PollCategory;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class PollCategoryController extends BackController
{
	public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
        	$this->user = Auth::user();
			if(config('bookdose.app.folder') != "gpo"){
				if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
					return $next($request);
				}
				else {
					return redirect()->route('home');
				}
			}else{
				if ($this->user->hasAnyPermission(['km.poll.manage'])) {
					return $next($request);
				}
				else {
					return redirect()->route('home');
				}
			}
     	});
 	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	return view('back.'.config('bookdose.theme_back').'.modules.poll.category.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	return view('back.'.config('bookdose.theme_back').'.modules.poll.category.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$validatedData = $request->validate([
    		'title' => 'required|max:255',
    		'slug' => 'nullable|alpha_dash|max:255',
    		'status' => 'boolean',
    	]);
	
		if(empty($validatedData['slug'])){
			$validatedData['slug'] = md5(uniqid(rand(), true));
		}
    	$validatedData['created_by'] = Auth::user()->id;
    	$validatedData['user_org_id'] = Auth::user()->user_org_id;

    	$category = PollCategory::create($validatedData);
    	if ($category) {
     			//--- Start log ---//
    		$log = collect([ (object)[
    			'module' => 'Category', 
    			'severity' => 'Info', 
    			'title' => 'Insert', 
    			'desc' => '[Succeeded] - '.$category->title,
    		]])->first();
    		parent::Log($log);
              //--- End log ---//

    		if ($request->save_option == '1')
    			return redirect()->route('admin.poll.category.index')->with('success', 'Category is successfully saved.');
    		else
    			return redirect()->route('admin.poll.category.create')->with('success', 'Category is successfully saved.');
    	}
    	return redirect()->route('admin.poll.category.create')->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	$category = PollCategory::findOrFail($id);
    	return view('back.'.config('bookdose.theme_back').'.modules.poll.category.form', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	$id = $request->input('id');
    	$validatedData = $request->validate([
    		'title' => 'required|max:255',
    		'slug' => 'nullable|alpha_dash|max:255',
    		'status' => 'boolean',
    	]);
		if(empty($validatedData['slug'])){
			$validatedData['slug'] = md5(uniqid(rand(), true));
		}
    	$validatedData['updated_by'] = Auth::user()->id;

    	PollCategory::where('id', $id)->update($validatedData);
         //--- Start log ---//
    	$log = collect([ (object)[
    		'module' => 'Category', 
    		'severity' => 'Info', 
    		'title' => 'Update', 
    		'desc' => '[Succeeded] - '.$validatedData['title'],
    	]])->first();
    	parent::Log($log);
          //--- End log ---//

    	return redirect()->route('admin.poll.category.edit', $id)->with('success', 'Category is successfully updated.');

    }

    public function ajaxGetData(Request $request)
    {

    	$query = PollCategory::myOrg()
    	->select(array_merge(
    		array('*'),
    		array(
    			DB::raw('DATE_FORMAT(poll_categories.created_at, "%d/%m/%Y") AS created_date'), 
    			DB::raw('DATE_FORMAT(poll_categories.updated_at, "%d/%m/%Y") AS updated_date') 
    		)
    	));
    	$datatable = new DataTables;
    	return $datatable->eloquent($query)

    	->addColumn('title_action', function ($category) {
    		return '<a href="'.route('admin.poll.category.edit', $category->id).'" class="">'.$category->title.'</a>';
    	})

    	->addColumn('status_html', function ($category) {
    		if ($category->status == 1)
    			return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
    		else 
    			return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
    	})
    	->addColumn('actions', function ($category) {
    		if ($category->status == 1)
    			return '
    		<span class="dropdown">
    		<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
    		<i class="la la-ellipsis-h"></i>
    		</a>
    		<div class="dropdown-menu dropdown-menu-right">
    		<a class="dropdown-item" href="javascript:;" data-id='.json_encode($category->id).' data-status='.json_encode($category->status).' data-title='.json_encode($category->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
    		<a class="dropdown-item" href="javascript:;" data-id='.json_encode($category->id).' data-title='.json_encode($category->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
    		</div>
    		</span>';
    		else
    			return '
    		<span class="dropdown">
    		<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
    		<i class="la la-ellipsis-h"></i>
    		</a>
    		<div class="dropdown-menu dropdown-menu-right">
    		<a class="dropdown-item" href="javascript:;" data-id='.json_encode($category->id).' data-status='.json_encode($category->status).' data-title='.json_encode($category->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
    		<a class="dropdown-item" href="javascript:;" data-id='.json_encode($category->id).' data-title='.json_encode($category->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
    		</div>
    		</span>';
    	})
    	->rawColumns(['title_action', 'status_html', 'actions'])
    	->addIndexColumn()
    	->make(true);
    }

    public function ajaxQuickSave(Request $request)
    {
    	$category = PollCategory::create([
    		'title' => trim($request->title),
    		'slug' => md5(uniqid(rand(), true)),
    		'status' => 1,
    		'user_org_id' => Auth::user()->user_org_id,
    		'created_by' => Auth::user()->id,
    	]);
    	$categories = PollCategory::myOrg()->active()->get();
    	$selected_id = $category->id ?? '';
    	return json_encode(array(
 				'status' => 200,
 				'html_categories' => '
 					<div class="col-3">
						<div class="kt-checkbox-inline">
							<label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand kt-checkbox--check-bold checked">
								 <input type="checkbox" name="poll_categories[]" value="'.$category->id.'" checked> 
								 	'.$category->title.'
								 <span></span>
							</label>
						 </div>
					</div>',
 			));
    }

    public function setStatus(Request $request)
    {
    	$id = $request->input('id');
    	if ($id > 0) {
    		$item = PollCategory::where('id', $id)->firstOrFail();
    		$update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
    		$rs = PollCategory::where('id', $id)->update($update_data);
    		if ($rs) {
	        		//--- Start log ---//
    			$log = collect([ (object)[
    				'module' => 'Category', 
    				'severity' => 'Info', 
    				'title' => 'Update status', 
    				'desc' => '[Succeeded] - '.$item->title,
    			]])->first();
    			parent::Log($log);
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
        //--- Start log ---//
    	$log = collect([ (object)[
    		'module' => 'Category', 
    		'severity' => 'Error', 
    		'title' => 'Update status', 
    		'desc' => '[Failed] - Invalid id = '.$id,
    	]])->first();
    	parent::Log($log);
	  		//--- End log ---//

    	return json_encode(array(
    		'status' => 500,
    		'notify_title' => 'Oops!',
    		'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
    		'notify_icon' => 'icon la la-warning',
    		'notify_type' => 'danger',
    	));
    }

    public function delete(Request $request)
    {
    	$id = $request->input('id');
    	if ($id > 0) {
    		$rs = PollCategory::findOrFail($id);
    		$item = $rs;
    		if ($rs) Storage::delete($rs->file_path);

    		$rs = PollCategory::where('id', $id)->delete();
    		if ($rs) {
        			//--- Start log ---//
    			$log = collect([ (object)[
    				'module' => 'Category', 
    				'severity' => 'Info', 
    				'title' => 'Delete', 
    				'desc' => '[Succeeded] - '.$item->title,
    			]])->first();
    			parent::Log($log);
			  		//--- End log ---//

    			return json_encode(array(
    				'status' => 200,
    				'notify_title' => 'Hooray!',
    				'notify_msg' => $item->title.' has been deleted successfully.',
    				'notify_icon' => 'icon la la-check-circle',
    				'notify_type' => 'success',
    			));
    		}
    	}
        	//--- Start log ---//
    	$log = collect([ (object)[
    		'module' => 'Category', 
    		'severity' => 'Error', 
    		'title' => 'Delete', 
    		'desc' => '[Failed] - Invalid id = '.$id,
    	]])->first();
    	parent::Log($log);
	  		//--- End log ---//

    	return json_encode(array(
    		'status' => 500,
    		'notify_title' => 'Oops!',
    		'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
    		'notify_icon' => 'icon la la-warning',
    		'notify_type' => 'danger',
    	));
    }
 }
