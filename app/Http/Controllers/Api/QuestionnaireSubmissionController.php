<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QuestionnaireSubmissionController extends ApiController
{

	public function store()
	{
		// 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

		if (empty(request()->token))
 			$return_data['msg'] = 'Missing token';
 		elseif (! $payload = parent::parseJWT())
 			$return_data['msg'] = 'Invalid token';
 		elseif (empty(request()->url))
 			$return_data['msg'] = 'Missing url';
 		elseif (empty(request()->lang))
 			$return_data['msg'] = 'Missing lang';
 		elseif (empty(request()->form_data))
 			$return_data['msg'] = 'Missing form_data';

		if (!empty($return_data['msg'])) {
 			return response()->json($return_data);
 		}

 		// 2. Query
 		$lang = (in_array(request()->lang, ['th', 'en']) ? request()->lang : 'th' );
 		if (Form::active()->where('slug', request()->url)->doesntExist()) {
			$return_data['msg'] = 'Invalid reward_id';
 			return response()->json($return_data);
 		}

 		$form = Form::active()->where('slug', request()->url)->first();
 		$submission = FormSubmission::create([
	 			"form_id" => $form->id,
	 			"user_id" => $payload->id,
	 			"data_fields" => request()->form_data,
	 			"status" => 0,
	 			"created_by" => $payload->id,
	 		]);

 		if ($submission) {
 			return response()->json( [
				'status' => 'success',
				'label' => [
					"msg" => __('questionnaire.submit_success', [], $lang),
				],
			]);
 		}
 		else {
			return response()->json( [
				'status' => 'error',
				'label' => [
					"msg" => __('questionnaire.submit_failed', [], $lang),
				],
			]);
		}
	}

}
