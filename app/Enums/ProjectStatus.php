<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Active = 'active';
    case Archived = 'archived';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('frontend.str.project_status_active'),
            self::Archived => __('frontend.str.project_status_archived'),
            self::Blocked => __('frontend.str.project_status_blocked'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Active => 'text-bg-success',
            self::Archived => 'text-bg-secondary',
            self::Blocked => 'text-bg-danger',
        };
    }

    public static function labelFor(?string $value): string
    {
        return self::tryFrom((string) $value)?->label() ?? (string) $value;
    }

    public static function badgeClassFor(?string $value): string
    {
        return self::tryFrom((string) $value)?->badgeClass() ?? 'text-bg-secondary';
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    public static function values(): array
    {
        return array_map(static fn (self $status): string => $status->value, self::cases());
    }
}
