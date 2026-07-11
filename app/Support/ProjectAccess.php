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
            UserRole::ProjectAdmin->value => $query->whereHas(
                'administrators',
                fn (Builder $administratorQuery): Builder => $administratorQuery->whereKey($user->id)
            ),
            UserRole::Moderator->value => $query->whereHas(
                'moderators',
                fn (Builder $moderatorQuery): Builder => $moderatorQuery->whereKey($user->id)
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

    public static function scopeReadySentQuery(Builder $query, ?User $user = null, string $readySentTable = 'ready_sent'): Builder
    {
        $user ??= Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role === UserRole::Admin->value) {
            return $query;
        }

        if (!in_array($user->role, [UserRole::OrganizationAdmin->value, UserRole::ProjectAdmin->value], true)) {
            return $query->whereRaw('1 = 0');
        }

        $projectIds = self::availableProjectIds($user);

        if ($projectIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereExists(function ($exists) use ($projectIds, $readySentTable): void {
            $exists
                ->selectRaw('1')
                ->from('project_subscriber')
                ->whereColumn('project_subscriber.subscriber_id', $readySentTable . '.subscriber_id')
                ->whereIn('project_subscriber.project_id', $projectIds);
        });
    }

    public static function scopeRedirectQuery(Builder $query, ?User $user = null, string $redirectTable = 'redirect'): Builder
    {
        $user ??= Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role === UserRole::Admin->value) {
            return $query;
        }

        if (!in_array($user->role, [UserRole::OrganizationAdmin->value, UserRole::ProjectAdmin->value], true)) {
            return $query->whereRaw('1 = 0');
        }

        $projectIds = self::availableProjectIds($user);

        if ($projectIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereExists(function ($exists) use ($projectIds, $redirectTable): void {
            $exists
                ->selectRaw('1')
                ->from('subscribers')
                ->join('project_subscriber', 'project_subscriber.subscriber_id', '=', 'subscribers.id')
                ->whereColumn('subscribers.email', $redirectTable . '.email')
                ->whereIn('project_subscriber.project_id', $projectIds);
            });
    }

    public static function scopeTemplateQuery(Builder $query, ?User $user = null): Builder
    {
        $user ??= Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role === UserRole::Admin->value) {
            return $query;
        }

        if (!in_array($user->role, [UserRole::OrganizationAdmin->value, UserRole::ProjectAdmin->value], true)) {
            return $query->whereRaw('1 = 0');
        }

        $projectIds = self::availableProjectIds($user);

        if ($projectIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('project_id', $projectIds);
    }

    public static function scopeScheduleQuery(Builder $query, ?User $user = null): Builder
    {
        $user ??= Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role === UserRole::Admin->value) {
            return $query;
        }

        if (!in_array($user->role, [UserRole::OrganizationAdmin->value, UserRole::ProjectAdmin->value], true)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas(
            'template',
            fn (Builder $templateQuery): Builder => self::scopeTemplateQuery($templateQuery, $user)
        );
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
