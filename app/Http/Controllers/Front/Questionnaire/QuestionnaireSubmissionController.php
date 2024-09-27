<?php

namespace App\Http\Controllers\Front\Questionnaire;

use Auth;
use Session;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use App\Models\User;
use App\Http\Controllers\Front\FrontController;
use Illuminate\Http\Request;
use App\Core\Queue\AppQueue;
use App\Jobs\SendInformFormSubmit;
use App\Models\UserOrg;

class QuestionnaireSubmissionController extends FrontController
{
	public function __construct()
	{
        parent::__construct();
	}

	public function form(Request $request, $slug)
	{
		$content = Form::with('fields')->where('slug', $slug)->firstOrFail();
		$breadcrumbs = [
	        __('menu.front.questionnaire') => "",
		];
		$footer = UserOrg::MyOrg()->first();
      // app()->setLocale('th');
      return view('front.'.config('bookdose.theme_front').'.modules.questionnaire.form', compact(
				'content', 'breadcrumbs','footer'
		));
	}

	public function view(Request $request, $id)
	{
		$breadcrumbs = [
	        __('menu.front.questionnaire') => "",
		];
		$form_submission = FormSubmission::findOrFail($id);
		$form_data = $form_submission->data_fields;
		$form = Form::with('fields')->findOrFail($form_submission->form_id);
		$sender = User::find($form_submission->created_by);
		$footer = UserOrg::MyOrg()->first();
		// echo '<pre>'; print_r($form_data); echo '</pre>'; exit;
		return view('front.'.config('bookdose.theme_front').'.modules.questionnaire.view', compact('form_data', 'sender', 'form', 'form_submission', 'breadcrumbs','footer'));
	}

	public function create()
	{
		return view('front.'.config('bookdose.theme_front').'.modules.questionnaire.form');
	}

	public function store(Request $request, $slug)
	{
		$form = Form::where('slug', $slug)->firstOrFail();
		$status = 'error';
		$_data = $request->toArray();
		unset($_data['_token']);

		$data = [];
		foreach ($_data as $key => $value) {
			$form_field_id = explode('_', $key)[1] ?? '';
			$data[$form_field_id] = $value;
		}

		$submission = FormSubmission::create([
			'user_id' => Auth::user()->id ?? NULL,
			'form_id' => $form->id,
			'data_fields' => $data,
			'created_by' => Auth::user()->id,
		]);
		if ($submission) {
			$status = 'success';

			$email_data_fields = [];
			foreach ($data as $k => $value) {
				$field = FormField::find($k);
				if ($field) {
					$email_data_fields[$field->label] = $value;
				}
			}

	 		// 1. Send email to sender
	 		/*
	 		$this_user = User::where('id', Auth::user()->id)->first();
  			if ($this_user && !empty($this_user->email)) {
				$job = (new SendInformFormSubmit($this_user, false, $form->title, $email_data_fields, $submission->created_at))
						 ->onQueue(AppQueue::getQWithPrefix(AppQueue::Default));
				dispatch($job);
			}
			*/

			// 2. Send email to inform admin
  			if (!empty($form->contact_email) && filter_var($form->contact_email, FILTER_VALIDATE_EMAIL)) {
					$job = (new SendInformFormSubmit($form->contact_email, Auth::user(), $form->title, $email_data_fields, $submission->created_at))
						 	->onQueue(AppQueue::getQWithPrefix(AppQueue::Default));
					dispatch($job);
			}
		}
		return redirect()->route('questionnaire.submit.complete', $status);
	}

	public function showResult($status)
	{
		$breadcrumbs = [
	        __('menu.front.questionnaire') => "",
		];
		$footer = UserOrg::MyOrg()->first();
		return view('front.'.config('bookdose.theme_front').'.modules.questionnaire.submit_complete', compact('status', 'breadcrumbs','footer'));
	}
}
