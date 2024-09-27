<?php

namespace App\Http\Controllers\Back\Referencelink;

use DB;
use Auth;
use App\Models\ReferenceLinkCategories;
use App\Models\UserGroup;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class ReferenceLinkCategoryController extends BackController
{
    public $user;
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
		  return view('back.'.config('bookdose.theme_back').'.modules.ref_link.category.list');
    }	

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {	
		return view('back.'.config('bookdose.theme_back').'.modules.ref_link.category.form');
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
				'slug' => 'required|max:255',
			    'status' => 'boolean',
				'weight' => 'required',
			]);
			// $validatedData['slug'] = Str::slug($validatedData['title'], '-');
			// if(empty($validatedData['slug']))
			// {
			// 	$validatedData['slug'] = $validatedData['title'];
			// }
			if ($request->cover_image_path) {
	    		$path = $request->cover_image_path->store('reference_links');
	    		$validatedData['cover_image_path'] = $path;
	    	}
			$validatedData['created_by'] = Auth::user()->id;
			$validatedData['updated_by'] = Auth::user()->id;
				$item = ReferenceLinkCategories::create($validatedData);
				if ($item) {
					//--- Start log ---//
					$log = collect([ (object)[
						'module' => 'Reference Link Categories', 
						'severity' => 'Info', 
						'title' => 'Insert', 
						'desc' => '[Succeeded] - '.$item->title,
					]])->first();
					parent::Log($log, Auth::user());
					//--- End log ---//
				}

        	if ($request->save_option == '1')
        		return redirect('/admin/reference-link/category/edit/'.$item->id)->with('success', request()->title.' is successfully saved.');
        	else
        		return redirect('/admin/reference-link/category/create')->with('success', request()->title.' is successfully saved.');
			
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CourseCategory  $courseCategory
     * @return \Illuminate\Http\Response
     */
    public function show(CourseCategory $courseCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CourseCategory  $courseCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        	$page_header = 'Edit Reference Link Categories';
    		$reference_Link_category = ReferenceLinkCategories::findOrFail($id);
    		
        	return view('back.'.config('bookdose.theme_back').'.modules.ref_link.category.form', compact( 'reference_Link_category', 'page_header'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CourseCategory  $courseCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    		$validatedData = $request->validate([
			    'title' => 'required|max:255',
				'slug' => 'required|max:255',
			    'status' => 'boolean',
				'weight' => 'required',
			]);
			$rs = ReferenceLinkCategories::findOrFail($id);
			// $validatedData['slug'] = Str::slug($validatedData['title'], '-') ;
			// if(empty($validatedData['slug']))
			// {
			// 	$validatedData['slug'] = $validatedData['title'];
			// }
			if ($request->cover_image_path) {
				 if ($rs) Storage::delete($rs->cover_image_path);
	    		$path = $request->cover_image_path->store('reference_links');
	    		$validatedData['cover_image_path'] = $path;
	    	}
			$validatedData['updated_by'] = Auth::user()->id;
        	$rs->update($validatedData);

        	//--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'Reference Link Categories', 
	      		'severity' => 'Info', 
	      		'title' => 'Update', 
	      		'desc' => '[Succeeded] - '.$validatedData['title'],
	   		]])->first();
	  		parent::Log($log, Auth::user());
	  		//--- End log ---//
        	return redirect('/admin/reference-link/category/edit/'.$id)->with('success', 'Reference Link Categories is successfully updated.');
    }
    
    public function setStatus(Request $request)
    {
    		$id = $request->input('id');
	      $item = ReferenceLinkCategories::find($id);
    		if ($item) {
	    		$update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
	        	$rs = ReferenceLinkCategories::where('id', $id)->update($update_data);
	        	if ($rs) {
	        		//--- Start log ---//
		    		$log = collect([ (object)[
			      		'module' => 'Reference Link Categories', 
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

    public function ajaxGetData() 
    {
		$query = DB::table('reference_link_categories')
				->select(array_merge(
					array('id', 'title', 'status','weight','cover_image_path'),
				));
		$datatable = new Datatables;
		return $datatable->queryBuilder($query)
			->addColumn('image', function ($reference_links) {
					return '<a href="'.route('admin.reference-link.category.edit', $reference_links->id).'" class="">
						<img src="'.asset('storage/'.$reference_links->cover_image_path).'" class="img-fluid" style="width:200px">
					</a>';
				})
				->addColumn('title_action', function ($reference_links) {
					return '<a href="'.route('admin.reference-link.category.edit', $reference_links->id).'" class="">'.$reference_links->title.'</a>';
				})
				->addColumn('status_html', function ($reference_links) {
				if ($reference_links->status == 1)
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
				else 
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
				})
				->addColumn('actions', function ($reference_links) {
				if ($reference_links->status == 1)
					return '
						<span class="dropdown">
							<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reference_links->id).' data-status='.json_encode($reference_links->status).' data-title='.json_encode($reference_links->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
								<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reference_links->id).' data-title='.json_encode($reference_links->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
							</div>
						</span>';
				else
					return '
						<span class="dropdown">
							<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reference_links->id).' data-status='.json_encode($reference_links->status).' data-title='.json_encode($reference_links->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
								<a class="dropdown-item" href="javascript:;" data-id='.json_encode($reference_links->id).' data-title='.json_encode($reference_links->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
							</div>
						</span>';
				
				})
				->rawColumns(['title_action', 'status_html', 'actions','image'])
				->addIndexColumn()
				->make(true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CourseCategory  $courseCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
 		$id = $request->input('id');
     	$item = ReferenceLinkCategories::find($id);
     	if ($item) {
     		$rs = ReferenceLinkCategories::where('id', $id)->delete();
     		if ($rs) {
     			//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'Reference Link Categories', 
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
