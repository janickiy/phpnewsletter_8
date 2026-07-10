<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case OrganizationAdmin = 'organization_admin';
    case ProjectAdmin = 'project_admin';
    case Moderator = 'moderator';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('frontend.str.admin'),
            self::OrganizationAdmin => __('frontend.str.organization_admin'),
            self::ProjectAdmin => __('frontend.str.project_admin'),
            self::Moderator => __('frontend.str.moderator'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => __('frontend.form.role_note_admin'),
            self::OrganizationAdmin => __('frontend.form.role_note_organization_admin'),
            self::ProjectAdmin => __('frontend.form.role_note_project_admin'),
            self::Moderator => __('frontend.form.role_note_moderator'),
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::Admin;
    }

    public static function labelFor(?string $value): string
    {
        return self::tryFrom((string) $value)?->label() ?? (string) $value;
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }

        return $options;
    }

    public static function values(): array
    {
        return array_map(static fn (self $role): string => $role->value, self::cases());
    }

    public static function descriptions(): array
    {
        return array_map(static fn (self $role): string => $role->description(), self::cases());
    }
}
