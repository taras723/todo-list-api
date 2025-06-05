<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize(): bool
    {
        return auth()->check();
    }

    // Validation rules for creating a task
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'integer', 'min:1', 'max:5'],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'parent_id' => ['nullable', 'integer', 'exists:tasks,id'],
        ];
    }
}