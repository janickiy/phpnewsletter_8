<?php

namespace App\DTO;

use App\Enums\ProjectStatus;

final class ProjectCreateData
{
    public function __construct(
        public readonly int $organizationId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ProjectStatus $status,
        public readonly ?string $defaultSenderName,
        public readonly ?string $defaultFromEmail,
        public readonly ?string $defaultReplyTo,
        public readonly ?string $timezone,
        public readonly ?int $unsubscribeTemplateId,
    ) {
    }

    public function toArray(): array
    {
        return [
            'organization_id' => $this->organizationId,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status->value,
            'default_sender_name' => $this->defaultSenderName,
            'default_from_email' => $this->defaultFromEmail,
            'default_reply_to' => $this->defaultReplyTo,
            'timezone' => $this->timezone,
            'unsubscribe_template_id' => $this->unsubscribeTemplateId,
        ];
    }
}
