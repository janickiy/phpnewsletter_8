<?php

namespace App\Http\Requests\Admin\Users;

use App\Enums\UserRole;
use App\Models\User;
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
            'id' => ['required', 'integer', 'exists:' . User::getTableName() . ',id'],
            'login' => [
                'required',
                'string',
                'max:255',
                'unique:' . User::getTableName() . ',login,' . $this->id
            ],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::in(UserRole::values())],
            'description' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6'],
            'password_again' => [
                'required_with:password',
                'nullable',
                'string',
                'min:6',
                'same:password'
            ],
        ];
    }
}
