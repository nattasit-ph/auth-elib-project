<?php

namespace App\Http\Controllers\Back\Poll;

use DB;
use Auth;
use Session;
use App\Models\Poll;
use App\Models\PollCategory;
use App\Models\PollOption;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Poll\PollsExport;
use App\Models\Module;

class PollController extends BackController
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
				if ($this->user->hasAnyPermission(['auth.poll.manage'])) {
					return $next($request);
				}
				else {
					return redirect()->route('home');
				}
			}

     	});
 	}
	 
	 public function index()
	{
        $all_category = PollCategory::myOrg()->orderBy('title', 'asc')->get();
		return view('back.'.config('bookdose.theme_back').'.modules.poll.list', compact('all_category'));
	}

	public function create()
	{
    	$categories = PollCategory::myOrg()->active()->orderBy('title', 'asc')->get();
		$module = Module::myOrg()->where('slug', 'poll')->first();
    	return view('back.'.config('bookdose.theme_back').'.modules.poll.form', compact('categories', 'module'));
 	}


	public function exportToExcel(Request $request) 
	{
		$status = $request->input('hd_status');
		$poll_start = $request->input('hd_poll_start') ?? '';
		$poll_end = $request->input('hd_poll_end') ?? '';
		$keyword = $request->input('hd_keyword') ?? '';
		$sort_by = ['sort_column' => session('sort_column'), 'sort_by' => session('sort_by')];
  		return Excel::download(new PollsExport($status, $poll_start, $poll_end, $keyword, $sort_by), 'report_poll_'.now().'.xlsx');
	}


	public function store(Request $request)
	{
    	$validatedData = $request->validate([
		    'question' => 'required|max:255',
		    'poll_start' => 'nullable',
		    'poll_end' => 'nullable',
		    'status' => 'boolean',
		]);
     	if (!empty($validatedData['poll_start'])) {
			$date = date_create_from_format("d/m/Y", $validatedData['poll_start']);
			$validatedData['poll_start'] =  date_format($date, "Y-m-d");
		}
		if (!empty($validatedData['poll_end'])) {
			$date = date_create_from_format("d/m/Y", $validatedData['poll_end']);
			$validatedData['poll_end'] =  date_format($date, "Y-m-d");
		}
		$validatedData['created_by'] = Auth::user()->id;
		
		if(config('bookdose.app.folder') != "gpo"){
			$validatedData['user_org_id'] = Auth::user()->user_org_id;
		}
		
		$poll = Poll::create($validatedData);
		if ($poll) {
			// Sync categories
			$poll->categories()->sync($request->poll_categories);
			//--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'Poll', 
	      		'severity' => 'Info', 
	      		'title' => 'Insert', 
	      		'desc' => '[Succeeded] - '.$poll->question,
	   		]])->first();
	  		parent::Log($log);
	  		//--- End log ---//

    		if ($request->save_option == '1')
        		return redirect()->route('admin.poll.edit', $poll->id)->with('success', 'Poll is successfully saved.');
        	else
        		return redirect()->route('admin.poll.create')->with('success', 'Poll is successfully saved.');
    	}
    	else {
    		return redirect()->route('admin.poll.create')->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');
    	}
	}

	public function edit($id)
	{	
		$module = Module::myOrg()->where('slug', 'poll')->first();
     	$page_header = 'Edit site poll';
		 $poll = Poll::with('categories')->findOrFail($id);
		 $poll_options = PollOption::where('poll_id', $id)->get();
		 $categories = PollCategory::myOrg()->active()->orderBy('title', 'asc')->get();
		 $selected_categories = [];
		 if (!empty($poll->categories)) {
			 foreach ($poll->categories as $cat) {
				 $selected_categories[] = $cat['pivot']['poll_category_id'];
			 }
		 }
		 $poll->selected_categories = $selected_categories;
		 return view('back.'.config('bookdose.theme_back').'.modules.poll.form', compact('poll', 'categories', 'poll_options', 'page_header', 'module'));
	}



	public function update(Request $request)
	{	
 		$id = $request->input('id');
		$poll = Poll::where('id', $id)->firstorFail();
 		$validatedData = $request->validate([
		    'question' => 'required|max:255',
		    'poll_start' => 'nullable',
		    'poll_end' => 'nullable',
		    'status' => 'boolean',
		]);
    	if (!empty($validatedData['poll_start'])) {
			$date = date_create_from_format("d/m/Y", $validatedData['poll_start']);
			$validatedData['poll_start'] =  date_format($date, "Y-m-d");
		}
		if (!empty($validatedData['poll_end'])) {
			$date = date_create_from_format("d/m/Y", $validatedData['poll_end']);
			$validatedData['poll_end'] =  date_format($date, "Y-m-d");
		}
		$validatedData['updated_by'] = Auth::user()->id;
		
  		// Update poll
  		$data_poll_option = $request->poll_option;
  		$data_poll_option_id = $request->poll_option_id;
  		$data_poll = $validatedData;
  		Poll::where('id', $id)->update($data_poll);
		
		// Sync categories
		$poll->categories()->sync($request->poll_categories);

  		//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Poll', 
      		'severity' => 'Info', 
      		'title' => 'Update', 
      		'desc' => '[Succeeded] - '.$data_poll['question'],
   		]])->first();
  		parent::Log($log);
  		//--- End log ---//

  		// Insert/Update poll_option
  		$total_options = 0;
  		if (!empty($data_poll_option) && is_array($data_poll_option)) {
  			unset($data_poll_option[0]);
  			unset($data_poll_option_id[0]);
  			if (!empty($data_poll_option) && is_array($data_poll_option)) {
  				// Delete missing id
  				$tmp = array_filter($data_poll_option_id, function($value)  { return !is_null($value) && $value !== ''; });
  				PollOption::where('poll_id', $id)->whereNotIn('id', $tmp)->delete();

  				// Insert/Update
  				foreach ($data_poll_option as $k => $value) {
  					if (!empty($value)) {
  						$total_options++;
  						$data = [];
     					$data['title'] = $value;
     					$data['poll_id'] = $id;
     					if ($data_poll_option_id[$k] > 0) {
     						PollOption::where('id', $data_poll_option_id[$k])->update($data);
							
     						//--- Start log ---//
				    		$log = collect([ (object)[
					      		'module' => 'Poll', 
					      		'severity' => 'Info', 
					      		'title' => 'Update option', 
					      		'desc' => '[Succeeded] - '.$value,
					   		]])->first();
					  		parent::Log($log);
					  		//--- End log ---//
     					}
     					else {
     						PollOption::create($data);
     						//--- Start log ---//
				    		$log = collect([ (object)[
					      		'module' => 'Poll', 
					      		'severity' => 'Info', 
					      		'title' => 'Insert option', 
					      		'desc' => '[Succeeded] - '.$value,
					   		]])->first();
					  		parent::Log($log);
					  		//--- End log ---//
     					}
  					}
  				}
  				Poll::where('id', $id)->update(['total_options' => $total_options]);
  			}
  		}
  		return redirect()->route('admin.poll.edit', $id)->with('success', 'Poll is successfully updated.');
	}

	public function setStatus(Request $request)
	{
 		$id = $request->input('id');
 		$poll = Poll::where('id', $id)->firstOrFail();
 		if ($id > 0) {
    		$update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
        	$rs = Poll::where('id', $id)->update($update_data);
        	if ($rs) {
        		//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'Poll', 
		      		'severity' => 'Info', 
		      		'title' => 'Update status', 
		      		'desc' => '[Succeeded] - '.$poll->question,
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
      		'module' => 'Poll', 
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

	public function ajaxGetData(Request $request) 
	{
		$order = $request->order;
		$columns = $request->columns;
		if (isset($columns) && isset($order[0]['column'])) {
			Session::put('sort_column', $columns[$order[0]['column']]['name']);
			Session::put('sort_by', $order[0]['dir']);
		}

		$filter_status = $request->filter_status ?? '';
		$filter_poll_start = $request->filter_poll_start ?? '';
		$filter_poll_end = $request->filter_poll_end ?? '';
 		$query = DB::table('polls')
	    		->select(array_merge(
	    			array('id', 'question', 'poll_start', 'poll_end', 'total_votes', 'total_options', 'status', 'created_at', 'updated_at'),
	    			array(
	    				DB::raw('IF (poll_start IS NOT NULL, DATE_FORMAT(polls.poll_start, "%d/%m/%Y"), NULL) AS poll_start_date'), 
	    				DB::raw('IF (poll_end IS NOT NULL, DATE_FORMAT(polls.poll_end, "%d/%m/%Y"), NULL) AS poll_end_date'), 
	    				DB::raw('DATE_FORMAT(polls.created_at, "%d/%m/%Y") AS created_date'), 
		      		DB::raw('DATE_FORMAT(polls.updated_at, "%d/%m/%Y") AS updated_date') 
	    			)
	    		));

		if ($filter_status !== '') {
			$query = $query->where('status', $filter_status);
		}

		if (!empty($filter_poll_start)) {
			$_arr = explode("/", $filter_poll_start);
			if (count($_arr) == 3) 
				$query = $query->where('poll_start', '>=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($filter_poll_end)) {
			$_arr = explode("/", $filter_poll_end);
			if (count($_arr) == 3) 
				$query = $query->where('poll_end', '<=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}
		$category = $request->input('category');
        if(!empty($category)){
            $query = $query->join('ref_poll_categories', 'ref_poll_categories.poll_id', '=', 'polls.id')
                            ->where('ref_poll_categories.poll_category_id', $category);
        }

	   $datatable = new DataTables;
	   return $datatable-> queryBuilder($query)
	    		
	         ->addColumn('title_action', function ($poll) {
	             return '<a href="'.route('admin.poll.edit', $poll->id).'" class="">'.$poll->question.'</a>';
	         })
	         ->addColumn('status_html', function ($poll) {
	         	if ($poll->status == 1)
	             	return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
	            else 
	            	return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
	         })
	         ->addColumn('actions', function ($poll) {
	         	if ($poll->status == 1)
	         		return '
			         	<span class="dropdown">
	                      <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
	                        <i class="la la-ellipsis-h"></i>
	                      </a>
	                      <div class="dropdown-menu dropdown-menu-right">
	                          	<a class="dropdown-item" href="javascript:;" data-id='.json_encode($poll->id).' data-status='.json_encode($poll->status).' data-title='.json_encode($poll->question, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
	                          	<a class="dropdown-item" href="javascript:;" data-id='.json_encode($poll->id).' data-title='.json_encode($poll->question, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
	                      </div>
	                  </span>';
	            else
	            	return '
			         	<span class="dropdown">
	                      <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
	                        <i class="la la-ellipsis-h"></i>
	                      </a>
	                      <div class="dropdown-menu dropdown-menu-right">
	                          	<a class="dropdown-item" href="javascript:;" data-id='.json_encode($poll->id).' data-status='.json_encode($poll->status).' data-title='.json_encode($poll->question, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
	                          	<a class="dropdown-item" href="javascript:;" data-id='.json_encode($poll->id).' data-title='.json_encode($poll->question, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
	                      </div>
	                  </span>';
	         })
	         ->rawColumns(['image', 'title_action', 'status_html', 'actions'])
	         ->addIndexColumn()
	    		->make(true);
 	}

 	public function destroy(Request $request)
 	{
    		$id = $request->input('id');
        	if ($id > 0) {
        		$rs = Poll::findOrFail($id);
        		$item = $rs;
				// if ($rs) Storage::delete($rs->image_path);
        		
        		$rs = Poll::where('id', $id)->delete();
        		if ($rs) {
        			//--- Start log ---//
		    		$log = collect([ (object)[
			      		'module' => 'Poll', 
			      		'severity' => 'Info', 
			      		'title' => 'Delete', 
			      		'desc' => '[Succeeded] - '.$item->question,
			   		]])->first();
			  		parent::Log($log);
			  		//--- End log ---//

	        		return json_encode(array(
					   'status' => 200,
					   'notify_title' => 'Hooray!',
				    	'notify_msg' => $item->question.' has been deleted successfully.',
	 				 	'notify_icon' => 'icon la la-check-circle',
	 				 	'notify_type' => 'success',
					));
	        	}
        	}
        	//--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'Poll', 
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

 	public function report(Request $request)
 	{

 	}

}
