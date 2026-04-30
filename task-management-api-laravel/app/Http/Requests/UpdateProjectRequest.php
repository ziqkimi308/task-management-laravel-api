<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
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
			// "sometimes" means evaluate this field, only if it is present in request data
			'name' => 'sometimes|string|max:200',
			'description'=>'nullable|string',
			'status'=>'sometimes|in:active,completed,archived',
			'color'=>'nullable|regex:/^[0-9A-F]{6}$/i',
			'deadline'=>'nullable|date|after:today'
		];
	}

	// Return message
	public function messages(): array
	{
		return [
			'color.regex' => 'Color must be a valid hex color (e.g., #3BB2F6)',
			'deadline.after' => 'Deadline must be in the future'
		];
	}
}
