<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	// This authorize is FormRequest own authorize mechanism.
	// The Gate::authorize() is related to Policy.
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'project_id'=>'required|exists:projects,id',
			'title'=>'required|string|max:255',
			'description'=>'nullable|string',
			'status'=>'in:todo,in_progress,completed',
			'priority'=>'in:low,medium,high,urgent',
			'due_date'=>'nullable|date'
		];
	}
}
