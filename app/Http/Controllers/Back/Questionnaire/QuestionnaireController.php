<?php

namespace App\Http\Controllers\Back\Questionnaire;

use DB;
use Auth;
use Session;
use App\Models\Form;
use App\Models\FormField;
use App\Models\User;
use App\Models\UserOrg;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Questionnaire\QuestionnairesExport;
use App\Models\FormSystem;

class QuestionnaireController extends Controller
{
	public function __construct()
	{
        parent::getModules();
		$this->middleware(function ($request, $next) {
        	$this->user = Auth::user();
    //     	if ($this->user->hasAnyPermission(['km.questionnaire.manage'])) {
    			return $next($request);
 			// }
 			// else {
 				// return redirect()->route('home');
 			// }
     	});
	}

	public function index(Request $request)
	{
        parent::getModules();
        $org_slug = Auth::user()->org->slug;

		return view('back.'.config('bookdose.theme_back').'.modules.questionnaire.list', compact('org_slug'));
	}

	public function create(Request $request)
	{
        parent::getModules();
        $org_slug = Auth::user()->org->slug;

		$step = 'general';
		$slug_init = 'questionnaire-'.date_format(today(), "Ymd");
		return view('back.'.config('bookdose.theme_back').'.modules.questionnaire.form', compact('org_slug', 'step', 'slug_init'));
	}

	public function store(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$validatedData = $request->validate([
			'title' => 'required|max:255',
			'description' => 'nullable',
			'slug' => 'nullable',
			'contact_email' => 'nullable|email',
			'status' => 'boolean',
		]);
		// $validatedData['description'] = clean_description_for_db($validatedData['description']);
		$validatedData['lang'] = 'th';
		$validatedData['created_by'] = Auth::user()->id;

		// Create slug
		$ii = 0;
		if (empty($request->slug)) {
			$validatedData['slug'] = Str::slug($validatedData['title']);
		} else {
			$validatedData['slug'] = Str::slug($request->slug);
		}
		while (empty($validatedData['slug']) || Form::where('slug', $validatedData['slug'])->where('lang', $validatedData['lang'])->exists()) {
			$validatedData['slug'] = Str::slug($validatedData['slug']).($ii > 0 ? '-'.$ii : '');
			if (empty($validatedData['slug'])) $validatedData['slug'] = 'questionnaire';
			$ii++;
		}

		$form = Form::create($validatedData);
		if ($form) {
	      $lang = $validatedData['lang'];

	      //--- Start log ---//
    		$log = collect([ (object)[
	      		'module' => 'Form',
	      		'severity' => 'Info',
	      		'title' => 'Insert - Questionnaire',
	      		'desc' => '[Succeeded] - '.$form->title,
	   		]])->first();
	  		parent::Log($log);
	  		//--- End log ---//
			return redirect()->route('admin.questionnaire.edit', [$org_slug, $form->id, 'step' => 'fields'])->with('success', $form->title.' is successfully saved.');
		}
		else {
			return redirect()->route('admin.questionnaire.create', $org_slug)->with('error', 'Oops! Something went wrong. Please refresh this page and then try again.');
		}
	}
	public function edit(Request $request, $org_slug, $id)
	{
        parent::getModules();
        $org_slug = Auth::user()->org->slug;

		$content = Form::where('id', $id)->firstOrFail();
		$page_header = 'Edit '.$content->title;
		$step = $request->step ?? 'general';
		$fields = [];
		switch ($step)
		{
			case 'general':
				break;

			case 'fields':
				$fields = FormField::where('form_id', $content->id)->get();
				break;

			case 'submissions':
				break;
		}

  		return view('back.'.config('bookdose.theme_back').'.modules.questionnaire.form', compact('org_slug', 'content', 'page_header', 'step', 'fields'));
	}

	public function update(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$step = $request->input('step') ?? 'general';
		switch ($step)
		{
			case 'general':
				$form_id = $request->id;
				$form = Form::findorFail($form_id);
				$validatedData = $request->validate([
					'title' => 'required|max:255',
					'description' => 'nullable',
					'slug' => 'nullable',
					'contact_email' => 'nullable|email',
					'status' => 'boolean',
				]);
				$validatedData['lang'] = 'th';
				$validatedData['updated_by'] = Auth::user()->id;

		    	// Update slug
		 		$ii = 0;
				if (empty($request->slug)) {
					$validatedData['slug'] = Str::slug($validatedData['title']);
				} else {
					$validatedData['slug'] = Str::slug($request->slug);
				}
				while (empty($validatedData['slug']) || Form::where('id', '<>', $form_id)->where('slug', $validatedData['slug'])->where('lang', $validatedData['lang'])->exists()) {
					$validatedData['slug'] = Str::slug($validatedData['slug']).($ii > 0 ? '-'.$ii : '');
					if (empty($validatedData['slug'])) $validatedData['slug'] = 'questionnaire';
					$ii++;
				}
				$form->update($validatedData);
				break;

			case 'fields':
				$form_id = $request->form_id;
				$form = Form::findorFail($form_id);
				// echo '<pre>'; print_r($request->all()); echo '</pre>'; exit;
				$arr_row_id = $request->row_id;
				$arr_id = $request->id;
				// echo '<pre>'; print_r($request->row_id); echo '</pre>';
				// echo '<pre>'; print_r($request->id); echo '</pre>'; exit;
				FormField::where('form_id', $form_id)->whereNotIn('id', $arr_id)->delete();
				foreach ($arr_row_id as $k=>$row_id) {
					$options = NULL;

					if (isset($request->{'input_type_'.$row_id})
						&& in_array($request->{'input_type_'.$row_id}, ['dropdown', 'checkbox', 'radio'])
					) {
						$options = $request->{'option_'.$row_id};
					}

					FormField::updateOrCreate(
						['id' => $arr_id[$k]],
						[
							'form_id' => $form_id,
							'section_label' => $request->{'section_label_'.$row_id} ?? NULL,
							'label' => $request->{'label_'.$row_id} ?? NULL,
							'input_type' => $request->{'input_type_'.$row_id} ?? NULL,
							'help_text' => $request->{'help_text_'.$row_id} ?? NULL,
							'options' => $options,
							'is_required' => $request->{'is_required_'.$row_id} ?? 0,
							'weight' => $k,
						]
					);
				}
				break;
		}

      //--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Form',
      		'severity' => 'Info',
      		'title' => 'Update - Questionnaire',
      		'desc' => '[Succeeded] - '.$form->title,
   		]])->first();
  		parent::Log($log);
  		//--- End log ---//

		return redirect()->route('admin.questionnaire.edit', [$org_slug, $form_id, 'step' => $step])->with('success', $form->title.' is successfully updated.');

	}

	public function preview(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$step = $request->step ?? 'general';
        switch ($step) {
            default:
            case 'general':
                $id = $request->id;
                break;
            case 'fields':
                $id = $request->form_id;
                break;
        }
        $content = Form::with('fields')->findorFail($id);
            $breadcrumbs = [
                __('menu.front.questionnaire') => "",
            ];
            $footer = UserOrg::MyOrg()->first();
        // app()->setLocale('th');
        return view('front.'.config('bookdose.theme_front').'.modules.questionnaire.form', compact('org_slug', 'content', 'breadcrumbs','footer'));
	}

	public function destroy(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$id = $request->input('id');
		if ($id > 0) {
			$rs = Form::where('id', $id)->firstOrFail();
			$item = $rs;
			$rs = Form::where('id', $id)->delete();
			if ($rs) {
				//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'Form',
		      		'severity' => 'Info',
		      		'title' => 'Delete - Questionnaire',
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
      		'module' => 'Form',
      		'severity' => 'Error',
      		'title' => 'Delete - Questionnaire',
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

	public function setStatus(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$id = $request->input('id');
		if ($id > 0) {
			$form = Form::where('id', $id)->firstOrFail();
			$update_data = array('status' => ($request->input('status') == '1' ? '0' : '1'));
			$rs = Form::where('id', $id)->update($update_data);
			if ($rs) {
				//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'Form',
		      		'severity' => 'Info',
		      		'title' => 'Update status - Questionnaire',
		      		'desc' => '[Succeeded] - '.$form->title,
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
      		'module' => 'Form',
      		'severity' => 'Error',
      		'title' => 'Update status - Questionnaire',
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
        $org_slug = Auth::user()->org->slug;

		$order = $request->order;
		$columns = $request->columns;
		if (isset($columns) && isset($order[0]['column'])) {
			Session::put('sort_column', $columns[$order[0]['column']]['name']);
			Session::put('sort_by', $order[0]['dir']);
		}

		$lang = $request->filter_lang ?? config('bookdose.frontend_default_lang');
		$filter_status = $request->filter_status ?? '';
		$query = Form::select(array_merge(
				array('*'),
				array(
					DB::raw('DATE_FORMAT(updated_at, "%d/%m/%Y") AS updated_date'),
				)
			))
			->withCount('submissions')
            ->withCount('systemBelib')
            ->withCount('systemKm')
            ->withCount('systemLearnext');
            // $query = $query->get();
            // dd($query);

		if ($filter_status !== '') {
			$query = $query->where('status', $filter_status);
		}

		$datatable = new DataTables;
		return $datatable
			->eloquent($query)
			->addColumn('title_action', function ($row) use ($lang, $org_slug) {
				return '<a href="'.route('admin.questionnaire.edit', [$org_slug, $row->id]).'" class="">'.$row->title.'</a>';
			})
            ->addColumn('belib_html', function ($row) {
				if ($row->system_belib_count == 1)
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success"></span>';
				else
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger"></span>';
			})
            ->addColumn('km_html', function ($row) {
				if ($row->system_km_count == 1)
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success"></span>';
				else
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger"></span>';
			})
            ->addColumn('learnext_html', function ($row) {
				if ($row->system_learnext_count == 1)
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success"></span>';
				else
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger"></span>';
			})
			->addColumn('status_html', function ($row) {
				if ($row->status == 1)
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Active</span>';
				else
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Inactive</span>';
			})
			->addColumn('approval_status_html', function ($row) {
				if ($row->approval_status == '1')
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Approved</span>';
				else if ($row->approval_status == '0')
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Rejected</span>';
				else
					return '<span class="kt-badge kt-badge--warning kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-warning">Waiting for Approval</span>';
			})
			->addColumn('actions', function ($row) use ($lang) {
				$html_replicate = '';
				$html_copy_url = '<a class="dropdown-item" href="javascript:;" data-url="'.(url('questionnaire/'.$row->slug)).'" onClick="copyUrl(this)"><i class="la la-globe"></i> Copy URL</a>';
                $html_set_belib = '';
                $html_set_km = '';
                $html_set_learnext ='';
                if(config('bookdose.app.belib_url')){
                    $html_set_belib = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-system="belib" data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleSystem(this)"><i class="la la-book"></i> Set As Belib</a>';
                }
                if(config('bookdose.app.km_url')){
                    $html_set_km = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-system="km" data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleSystem(this)"><i class="la la-stack-overflow"></i> Set As KM</a>';
                }
                if(config('bookdose.app.learnext_url')){
                    $html_set_learnext = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-system="learnext" data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleSystem(this)"><i class="la la-graduation-cap"></i> Set As Learnext</a>';
                }

				if ($row->status == 1) {
					$html = '
					<span class="dropdown">
						<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							'.$html_replicate.'
							<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye-slash"></i> Inactivate</a>
							'.$html_copy_url.'
                            '.$html_set_belib.'
                            '.$html_set_km.'
                            '.$html_set_learnext.'
							<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
						</div>
					</span>';
				}
				else {
					$html = '
					<span class="dropdown">
						<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							'.$html_replicate.'
							<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-status='.json_encode($row->status).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="toggleStatus(this)"><i class="la la-eye"></i> Activate</a>
							'.$html_copy_url.'
                            '.$html_set_belib.'
                            '.$html_set_km.'
                            '.$html_set_learnext.'
							<a class="dropdown-item" href="javascript:;" data-id='.json_encode($row->id).' data-title='.json_encode($row->title, JSON_UNESCAPED_UNICODE).' onClick="deleteItem(this)"><i class="la la-trash"></i> Delete</a>
						</div>
					</span>';
				}
				return $html;
			})
			->rawColumns(['title_action', 'status_html', 'approval_status_html', 'actions', 'belib_html', 'km_html', 'learnext_html'])
			->addIndexColumn()
			->make(true);
	}

	public function ajaxGetDataJSON(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$order_column = $request->order_column ?? 'title';
		$order_by = $request->order_by ?? 'asc';
		$data = Form::active()
			// ->lang($lang)
			->orderBy($order_column, $order_by)
			->get()
			->transform(function ($item, $key) {
				$_item = [];
				$_item['id'] = $item->id;
				$_item['text'] = $item->title;
				$_item['slug'] = $item->slug;
				return $_item;
			});

		if ($request->display_all_items_option ?? FALSE) {
			$data->prepend(['id' => '', 'text' => 'ทุกรายการ']);
		}
		// echo '<pre>'; print_r($all_file_list); echo '</pre>'; exit;
		return response()->json(['results' => $data]);
	}

	public function exportToExcel(Request $request)
	{
		$lang = $request->input('hd_lang');
		$status = $request->input('hd_status');
		$keyword = $request->input('hd_keyword') ?? '';
		$sort_by = ['sort_column' => session('sort_column'), 'sort_by' => session('sort_by')];
  		return Excel::download(new QuestionnairesExport($status, $keyword, $sort_by), 'report_questionnaire_'.now().'.xlsx');
	}

    public function setSystem(Request $request)
    {
        $id = $request->input('id');
        $system = $request->input('system');
		if ($id > 0) {
			$form = FormSystem::myOrg()->where('system', $system)->where('form_id', $id)->first();
            $update_data = [];
            $update_data['form_id'] = $id;
            if($form){
                $update_data['form_id'] = NULL;
            }
			$rs = FormSystem::where('system', $system)->update($update_data);
			if ($rs) {
				//--- Start log ---//
	    		$log = collect([ (object)[
		      		'module' => 'Form',
		      		'severity' => 'Info',
		      		'title' => 'Update system - Questionnaire',
		      		'desc' => '[Succeeded] - '.$id,
		   		]])->first();
		  		parent::Log($log);
		  		//--- End log ---//

				return json_encode(array(
					'status' => 200,
					'notify_title' => 'Hooray!',
					'notify_msg' => 'System has been updated successfully.',
					'notify_icon' => 'icon la la-check-circle',
					'notify_type' => 'success',
				));
			}
		}
		//--- Start log ---//
 		$log = collect([ (object)[
      		'module' => 'Form',
      		'severity' => 'Error',
      		'title' => 'Update system - Questionnaire',
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
