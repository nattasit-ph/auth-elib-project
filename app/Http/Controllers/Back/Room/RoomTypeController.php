<?php

namespace App\Http\Controllers\Back\Room;

use DB;
use Auth;
use Session;
use App\Models\RoomType;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class RoomTypeController extends BackController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    public function index(Request $request)
	{
        return view('back.'.config('bookdose.theme_back').'.modules.room.type.list');
	}

    public function create(Request $request)
	{
		$step = 'general';
        return view('back.'.config('bookdose.theme_back').'.modules.room.type.form', compact('step'));
	}

    public function store(Request $request)
	{
       
		$validatedData = $request->validate([
			'title' => 'required|max:255',
            'description' => 'nullable',
			'weight' => 'nullable',
			'status' => 'boolean',
		]);
		
        $validatedData['user_org_id'] = Auth::user()->user_org_id;
		$validatedData['created_by'] = Auth::user()->id;
        $weight = $validatedData['weight'];

        $check_weight = RoomType::where('weight', $weight)
        ->first();
        $max_weight = RoomType::max('weight');
		

		//($max_weight);
        if(empty($weight)){
           $weight = 10 + $max_weight;
        }
    
        if(!empty($check_weight)){
            $weight = $max_weight + 10;
        }
        $validatedData['weight'] = $weight;

		$data = $validatedData;
		$room_type = RoomType::create($data);
		if ($room_type) {

	      //--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'RoomType', 
	      		'severity' => 'Info', 
	      		'title' => 'Insert', 
	      		'desc' => '[Succeeded] - '.$room_type->title,
	   		]])->first();
	  		parent::Log($log);
	  		//--- End log ---//
			  return redirect()->route('admin.roomType.index')->with('success', $validatedData['title'].' is successfully saved.');
	   }
	   else {
			return redirect()->route('admin.roomType.create')->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');
		}
	}

    public function ajaxGetData(Request $request) 
	{

		$order = $request->order;
		$query = RoomType::myOrg()->select(array_merge(
				array('room_types.*')
			));


		$datatable = new DataTables;
		return $datatable
			->eloquent($query)
		
			->addColumn('title_action', function ($row) {
				return '<a href="'.route('admin.roomType.edit', [$row->id]).'" class="">'.$row->title.'</a>';
			})
			->addColumn('description_html', function ($row) {
				return $row->description;
			})
            ->addColumn('weight_html', function ($row) {
				return $row->weight;
			})
			->addColumn('status_html', function ($row) {
				if ($row->status == 1)
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
				else 
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
			 })
			->addColumn('actions', function ($row) {
				if ($row->status == 1)
					return '
						<span class="dropdown">
						  <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
						  </a>
						  <div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
								<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
						  </div>
					  </span>';
				else
					return '
						<span class="dropdown">
						  <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
						  </a>
						  <div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
								<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
						  </div>
					  </span>';
				})
			->rawColumns(['title_action', 'description_html', 'weight_html', 'status_html', 'actions'])
			->addIndexColumn()
			->make(true);
			
	}

	public function setStatus(Request $request)
    {
        $id = $request->input('id');
        if (!empty($id)) {
            $room_type = RoomType::where('id', $id)->firstOrFail();
            $update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
            $rs = RoomType::where('id', $id)->update($update_data);
            if ($rs) {
                //--- Start log ---//
                 $log = collect([ (object)[
                      'module' => 'RoomType', 
                      'severity' => 'Info', 
                      'title' => 'Update status', 
                      'desc' => '[Succeeded] - '.$room_type->title,
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
              'module' => 'RoomType', 
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
		if (!empty($id)) {
			$rs = RoomType::where('id', $id)->firstOrFail();

			$item = $rs;

		

			$rs = RoomType::where('id', $id)->delete();
			if ($rs) {
				//--- Start log ---//
		 		$log = collect([ (object)[
		      		'module' => 'Room', 
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
      		'module' => 'Room', 
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

    public function edit(Request $request)
	{
        $id = $request->room_type;
		$step = $request->step ?? 'general';	

		$room_type = RoomType::where('id', $id)->first();
        // dd($room_type);
	
		$page_header = 'Edit '.$room_type->title;
        return view('back.'.config('bookdose.theme_back').'.modules.room.type.form', compact('step', 'page_header', 'room_type'));
	}

    public function update(Request $request)
	{
        $id = $request->input('room_type_id');
		$room_type = RoomType::findorFail($id);
        // dd($room_type);
		$validatedData = $request->validate([
			'title' => 'required|max:255',
            'description' => 'nullable',
			'weight' => 'nullable',
			'status' => 'boolean',
		]);
		
		$validatedData['updated_by'] = Auth::user()->id;
        $weight = $validatedData['weight'];
        if(empty($weight)){
            $weight = 0;
        }
       
        $validatedData['weight'] = $weight;
		
		$data = $validatedData;

		$room_type->update($data);
		//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'RoomType', 
      		'severity' => 'Info', 
      		'title' => 'Update', 
      		'desc' => '[Succeeded] - '.$data['title'],
   		]])->first();
  		parent::Log($log);
  		//--- End log ---//
        //   return view('backend.room.type.form', compact('step', 'page_header', 'room_type'));
		return redirect()->route('admin.roomType.index')->with('success', $data['title'].' is successfully updated.');
	}
}
