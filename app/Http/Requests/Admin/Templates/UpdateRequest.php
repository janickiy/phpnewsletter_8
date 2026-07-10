<?php

namespace App\Http\Requests\Admin\Templates;

use App\Models\Project;
use App\Models\Templates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateRequest extends FormRequest
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
            'id' => [
                'required',
                'integer',
                'exists:' . Templates::getTableName() .',id',
            ],
            'project_id' => [
                'required',
                'integer',
                Rule::exists(Project::getTableName(), 'id'),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'body' => [
                'required',
                'string',
            ],
            'prior' => [
                'required',
                'integer',
            ],
        ];
    }
}
