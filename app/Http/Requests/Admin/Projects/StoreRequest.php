<?php

namespace App\Http\Requests\Admin\Projects;

use App\Models\Project;
use App\Models\Templates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(Project::statusValues())],
            'default_sender_name' => ['nullable', 'string', 'max:255'],
            'default_from_email' => ['nullable', 'email', 'max:255'],
            'default_reply_to' => ['nullable', 'email', 'max:255'],
            'timezone' => ['nullable', 'timezone'],
            'locale' => ['nullable', 'string', Rule::in(array_keys(config('app.languages', [])))],
            'unsubscribe_template_id' => [
                'nullable',
                'integer',
                Rule::exists(Templates::getTableName(), 'id'),
            ],
        ];
    }
}
