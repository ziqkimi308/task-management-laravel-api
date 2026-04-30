<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
			'project_id' => 'sometimes|exists:projects,id',
			'title' => 'sometimes|string|max:255',
			'description' => 'nullable|string',
			'status' => 'sometimes|in:todo,in_progress,completed',
			'priority' => 'sometimes|in:low,medium,high,urgent',
			'due_date' => 'sometimes|nullable|date'
        ];
    }
}
