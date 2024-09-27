<?php
namespace App\Exports\Questionnaire;

use DB;
use App\Models\FormSubmission;
use App\Models\FormField;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class QuestionnaireSubmissionsExport implements FromView, WithTitle
{
   public function __construct($form_id, $status="", $submitted_start="", $submitted_end="", $keyword="", $sort="")
	{
	  $this->form_id = $form_id;
	  $this->status = $status;
	  $this->submitted_start = $submitted_start;
	  $this->submitted_end = $submitted_end;
	  $this->keyword = $keyword;
	  $this->sort = $sort;
	}

	public function title(): string
 	{
 		return 'Questionnaire Submissions';
 	}

   public function view(): View
	{
		$form_fields = FormField::where('form_id', $this->form_id)->whereNull('section_label')->get()->keyBy('id');
		$query = FormSubmission::where('form_id', $this->form_id)
			->select(array_merge(
				array('*'),
				array(
					DB::raw('DATE_FORMAT(form_submissions.created_at, "%d/%m/%Y %H:%i") AS created_date'),
				)
			))
			->with(['creator']);

		if ($this->status !== '' && !is_null($this->status)) {
			$query = $query->where('form_submissions.status', $this->status);
		}
		
		if (!empty($this->submitted_start)) {
			$_arr = explode("/", $this->submitted_start);
			if (count($_arr) == 3) 
				$query = $query->whereDate('form_submissions.created_at', '>=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($this->submitted_end)) {
			$_arr = explode("/", $this->submitted_end);
			if (count($_arr) == 3) 
				$query = $query->whereDate('form_submissions.created_at', '<=', $_arr[2].'-'.$_arr[1].'-'.$_arr[0]);
		}

		if (!empty($this->keyword)) {
			$query = $query->where('users.name', 'LIKE', '%'.$this->keyword.'%');
		}

		if (!empty($this->sort) && isset($this->sort['sort_column']) && isset($this->sort['sort_by'])) {
			// $sort_col = str_replace('creator.name', 'users.name', $this->sort['sort_column']);
			$results = $query->orderBy($this->sort['sort_column'], $this->sort['sort_by'])->get();
		}
		else {
    		$results = $query->orderBy('created_at', 'desc')->get();
		}

		// echo '<pre>'; print_r($query->toSql()); echo '</pre>'; exit;
		// echo '<pre>'; print_r($results->toArray()); echo '</pre>'; exit;
    	ob_end_clean();
	  	return view('back.export.questionnaire.submission', [
	      'results' => $results,
	      'form_fields' => $form_fields,
	  	]);
	}
}