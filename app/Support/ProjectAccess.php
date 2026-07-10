<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProjectAccess
{
    public static function availableProjectsQuery(?User $user = null): Builder
    {
        $user ??= Auth::user();

        $query = Project::query()
            ->with('organization:id,name,owner_id')
            ->orderBy('name');

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        return match ($user->role) {
            UserRole::Admin->value => $query,
            UserRole::OrganizationAdmin->value => $query->whereHas(
                'organization',
                fn (Builder $organizationQuery): Builder => self::organizationAccessConstraint($organizationQuery, $user)
            ),
            UserRole::ProjectAdmin->value,
            UserRole::Moderator->value => $query->whereHas(
                'administrators',
                fn (Builder $administratorQuery): Builder => $administratorQuery->whereKey($user->id)
            ),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public static function availableProjectIds(?User $user = null): array
    {
        return self::availableProjectsQuery($user)
            ->pluck('projects.id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    public static function optionGroups(?User $user = null): array
    {
        $user ??= Auth::user();

        if (!$user) {
            return [];
        }

        if (in_array($user->role, [UserRole::ProjectAdmin->value, UserRole::Moderator->value], true)) {
            $projects = self::availableProjectsQuery($user)
                ->get()
                ->map(static fn (Project $project): array => [
                    'id' => $project->id,
                    'name' => $project->name,
                ])
                ->values()
                ->all();

            return $projects === []
                ? []
                : [
                    [
                        'label' => null,
                        'projects' => $projects,
                    ],
                ];
        }

        $organizations = Organization::query()
            ->with(['projects' => static fn ($query) => $query->orderBy('name')])
            ->orderBy('name');

        if ($user->role !== UserRole::Admin->value) {
            $organizations->where(
                fn (Builder $query): Builder => self::organizationAccessConstraint($query, $user)
            );
        }

        return $organizations
            ->get()
            ->map(static fn (Organization $organization): array => [
                'label' => $organization->name,
                'projects' => $organization->projects
                    ->map(static fn (Project $project): array => [
                        'id' => $project->id,
                        'name' => $project->name,
                    ])
                    ->values()
                    ->all(),
            ])
            ->filter(static fn (array $group): bool => $group['projects'] !== [])
            ->values()
            ->all();
    }

    public static function userIsAdmin(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $user?->role === UserRole::Admin->value;
    }

    private static function organizationAccessConstraint(Builder $query, User $user): Builder
    {
        return $query
            ->where('owner_id', $user->id)
            ->orWhereHas('administrators', fn (Builder $administratorQuery): Builder => $administratorQuery->whereKey($user->id));
    }
}
