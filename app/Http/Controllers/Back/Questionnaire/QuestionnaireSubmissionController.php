<?php

namespace App\Http\Controllers\Back\Questionnaire;

use DB;
use Auth;
use Session;
use App\Models\FormSubmission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Questionnaire\QuestionnaireSubmissionsExport;

class QuestionnaireSubmissionController extends Controller
{
	public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
 			return $next($request);
		});
 	}

	public function ajaxGetData(Request $request) 
	{
		$form_id = $request->form_id;
		$order = $request->order;
		$columns = $request->columns;
		if (isset($columns) && isset($order[0]['column'])) {
			Session::put('sort_column', $columns[$order[0]['column']]['name']);
			Session::put('sort_by', $order[0]['dir']);
		}

		$filter_status = $request->filter_status ?? '';
		$filter_submitted_start = $request->filter_submitted_start ?? '';
		$filter_submitted_end = $request->filter_submitted_end ?? '';
		$query = FormSubmission::with('creator')
			->where('form_id', $form_id)
			->select(array_merge(
				array('*'),
				array(
					DB::raw('DATE_FORMAT(form_submissions.created_at, "%d/%m/%Y %H:%i") AS created_date'),
					DB::raw('DATE_FORMAT(form_submissions.updated_at, "%d/%m/%Y %H:%i") AS updated_date'),
				)
			));

		if ($filter_status !== '') {
			$query = $query->where('status', $filter_status);
		}

		if (!empty($filter_submitted_start)) {
			$_arr = explode("/", $filter_submitted_start);
			if (count($_arr) == 3) 
				$query = $query->whereDate('created_at', '>=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($filter_submitted_end)) {
			$_arr = explode("/", $filter_submitted_end);
			if (count($_arr) == 3) 
				$query = $query->whereDate('created_at', '<=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		$datatable = new DataTables;
		return $datatable
			->eloquent($query)
			->addColumn('title_action', function ($row) {
				return '<a href="'.route('questionnaire.view', [$row->id]).'" class="" target="_blank">'.$row->creator->name.'</a>';
			})
			->addColumn('status_html', function ($row) {
				switch ($row->status) {
					case 0: // waiting for review
						return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">รอการอนุมัติ</span>';
						break;
					case 1: // reviewing
						return '<span class="kt-badge kt-badge--warning kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-warning">กำลังดำเนินการ</span>';
						break;
					case 2: // completed
						return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">ดำเนินการเรียบร้อย</span>';
						break;
				}
			})
			->addColumn('actions', function ($row) {
				$html_status = '';
				/*
				switch ($row->status) {
					case 0: // waiting for review
						$html_status = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode('the request from '.$row->creator->name, JSON_UNESCAPED_UNICODE).' onClick="toggleFormStatus(this, 1, \'กำลังดำเนินการ\')"><i class="far fa-hourglass-half"></i> กำลังดำเนินการ</a>';
						break;
					case 1: // reviewing
						$html_status = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode('the request from '.$row->creator->name, JSON_UNESCAPED_UNICODE).' onClick="toggleFormStatus(this, 2, \'ดำเนินการเรียบร้อย\')"><i class="la la-check"></i> ดำเนินการเรียบร้อย</a>';
						break;
					case 2: // completed
						$html_status = '';
						break;
				}
				*/

				return '
					<span class="dropdown">
						<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">'.
							$html_status.
							'<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-title='.json_encode('the request from '.$row->creator->name, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
						</div>
					</span>';
			})
			->rawColumns(['title_action', 'status_html', 'actions'])
			->addIndexColumn()
			->make(true);
	}

	public function setStatus(Request $request)
	{
		$id = $request->input('id');
		if ($id > 0) {
			$edd = FormSubmission::where('id', $id)->firstOrFail();
			$update_data = array('status' => $request->input('new_status'));
			$rs = FormSubmission::where('id', $id)->update($update_data);
			if ($rs) {
				//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'FormSubmission', 
		      		'severity' => 'Info', 
		      		'title' => 'Update status - Form', 
		      		'desc' => '[Succeeded] - Update status from '.$edd->status. ' to ' . $request->input('new_status') .' (id = '.$edd->id.')',
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
      		'module' => 'FormSubmission', 
      		'severity' => 'Error', 
      		'title' => 'Update status - Form', 
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

	public function destroy(Request $request)
	{
		$id = $request->input('id');
		if ($id > 0) {
			$rs = FormSubmission::where('id', $id)->with('creator')->firstOrFail();
			$item = $rs;

			$rs = FormSubmission::where('id', $id)->delete();
			if ($rs) {
				//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'FormSubmission', 
		      		'severity' => 'Info', 
		      		'title' => 'Delete - Form', 
		      		'desc' => '[Succeeded] - id = '.$item->id,
		   		]])->first();
		  		parent::Log($log);
		  		//--- End log ---//

				return json_encode(array(
					'status' => 200,
					'notify_title' => 'Hooray!',
					'notify_msg' => 'FormSubmission request from '.$item->creator->name.' has been deleted successfully.',
					'notify_icon' => 'icon la la-check-circle',
					'notify_type' => 'success',
				));
			}
		}
		//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'FormSubmission', 
      		'severity' => 'Error', 
      		'title' => 'Delete - Form', 
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

	public function exportToExcel(Request $request) 
	{
		// $lang = $request->input('hd_lang');
		$form_id = $request->input('hd_form_id');
		$status = $request->input('hd_status');
		$submitted_start = $request->input('hd_submitted_start');
		$submitted_end = $request->input('hd_submitted_end');
		$keyword = $request->input('hd_keyword') ?? '';
		$sort_by = ['sort_column' => session('sort_column'), 'sort_by' => session('sort_by')];
  		return Excel::download(new QuestionnaireSubmissionsExport($form_id, $status, $submitted_start, $submitted_end, $keyword, $sort_by), 'report_form_submissions_'.now().'.xlsx');
	}

}
