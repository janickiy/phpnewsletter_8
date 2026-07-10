<?php

namespace App\DTO;

final class OrganizationCreateData
{
    public function __construct(
        public readonly int $ownerId,
        public readonly string $name,
        public readonly ?string $description,
    ) {
    }

    public function toArray(): array
    {
        return [
            'owner_id' => $this->ownerId,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
