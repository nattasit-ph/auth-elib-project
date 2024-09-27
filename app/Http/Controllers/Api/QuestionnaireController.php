<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\Form;
use App\Models\FormField;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QuestionnaireController extends ApiController
{

	public function form()
	{
		// 1. Pre-check parameters
		$return_data = array('status' => 'error', 'msg' => '');

		if (empty(request()->token))
			$return_data['msg'] = 'Missing token';
		elseif (!$payload = parent::parseJWT())
			$return_data['msg'] = 'Invalid token';

		if (!empty($return_data['msg'])) {
			return response()->json($return_data);
		}

		// 2. Query
		if (Form::active()->where('slug', request()->url)->doesntExist() && request()->exists('url')) {
			$return_data['msg'] = 'Invalid url';
			return response()->json($return_data);
		} else if (!request()->exists('url')) {
			$questionnaire = Form::Active()->select('slug')->orderBy('updated_at', 'desc')->first();
			request()->url = $questionnaire->slug;
		}
		$results = Form::active()->with(['fields'])
			->select('id', 'title', 'description', 'slug')
			->where('slug', request()->url)
			->get()
			->each(function ($item, $i) {
				foreach ($item->fields as $field) {
					$field['section_label'] = is_null($field['section_label']) ? '' : $field['section_label'];
					$field['label'] = is_null($field['label']) ? '' : $field['label'];
					$field['input_type'] = is_null($field['input_type']) ? '' : $field['input_type'];
					$field['help_text'] = is_null($field['help_text']) ? '' : $field['help_text'];
					$field['options'] = is_null($field['options']) ? [] : $field['options'];
					unset($field['created_at']);
					unset($field['updated_at']);
					unset($field['form_id']);
				}
			});

		return response()->json([
			'status' => 'success',
			'results' => $results ?? [],
		]);
	}
}
