<?php

namespace App\Http\Requests\Admin\Projects;

use App\DTO\ProjectCreateData;
use App\Enums\ProjectStatus;
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
            'status' => ['required', 'string', Rule::in(ProjectStatus::values())],
            'default_sender_name' => ['nullable', 'string', 'max:255'],
            'default_from_email' => ['nullable', 'email', 'max:255'],
            'default_reply_to' => ['nullable', 'email', 'max:255'],
            'timezone' => ['nullable', 'timezone'],
            'unsubscribe_template_id' => [
                'nullable',
                'integer',
                Rule::exists(Templates::getTableName(), 'id'),
            ],
        ];
    }

    public function toDto(int $organizationId): ProjectCreateData
    {
        $data = $this->validated();

        return new ProjectCreateData(
            organizationId: $organizationId,
            name: (string) $data['name'],
            description: $data['description'] ?? null,
            status: ProjectStatus::from((string) $data['status']),
            defaultSenderName: $data['default_sender_name'] ?? null,
            defaultFromEmail: $data['default_from_email'] ?? null,
            defaultReplyTo: $data['default_reply_to'] ?? null,
            timezone: $data['timezone'] ?? null,
            unsubscribeTemplateId: isset($data['unsubscribe_template_id'])
                ? (int) $data['unsubscribe_template_id']
                : null,
        );
    }
}
