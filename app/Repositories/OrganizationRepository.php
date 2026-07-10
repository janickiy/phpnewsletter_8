<?php

namespace App\Repositories;

use App\DTO\OrganizationCreateData;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class OrganizationRepository extends BaseRepository
{
    public function __construct(Organization $model)
    {
        parent::__construct($model);
    }

    public function queryForUser(?User $user): Builder
    {
        $query = $this->model->newQuery();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role !== UserRole::Admin->value) {
            $query->where(function (Builder $query) use ($user): void {
                $query
                    ->where('owner_id', $user->id)
                    ->orWhereHas('administrators', function (Builder $query) use ($user): void {
                        $query->whereKey($user->id);
                    });
            });
        }

        return $query;
    }

    public function createFromData(OrganizationCreateData $data): Organization
    {
        return $this->model->newQuery()->create($data->toArray());
    }

    public function updateFromData(int $id, OrganizationCreateData $data): bool
    {
        return $this->update($id, $data->toArray());
    }

    public function loadDetails(Organization $organization): Organization
    {
        return $organization->load([
            'owner:id,name,login',
            'administrators:id,name,login,role',
            'projects' => fn ($query) => $query->orderBy('name'),
        ]);
    }

    public function ownerOptions(?User $user): array
    {
        $query = User::query();

        if (!$user) {
            return [];
        }

        if ($user->role !== UserRole::Admin->value) {
            $query->whereKey($user->id);
        }

        return $query
            ->orderBy('name')
            ->orderBy('login')
            ->get(['id', 'name', 'login'])
            ->mapWithKeys(fn (User $user): array => [$user->id => $this->userOptionLabel($user)])
            ->toArray();
    }

    public function administratorOptions(Organization $organization): array
    {
        $attachedIds = $organization->administrators
            ->pluck('id')
            ->push($organization->owner_id)
            ->filter()
            ->unique()
            ->all();

        return User::query()
            ->whereIn('role', [
                UserRole::Admin->value,
                UserRole::OrganizationAdmin->value,
            ])
            ->when($attachedIds !== [], fn (Builder $query) => $query->whereNotIn('id', $attachedIds))
            ->orderBy('name')
            ->orderBy('login')
            ->get(['id', 'name', 'login'])
            ->mapWithKeys(fn (User $user): array => [$user->id => $this->userOptionLabel($user)])
            ->toArray();
    }

    public function attachAdministrator(Organization $organization, int $userId): void
    {
        if ($userId !== (int) $organization->owner_id) {
            $organization->administrators()->syncWithoutDetaching([$userId]);
        }
    }

    public function detachAdministrator(Organization $organization, User $user): void
    {
        $organization->administrators()->detach($user->id);
    }

    private function userOptionLabel(User $user): string
    {
        $name = trim((string) $user->name);
        $login = (string) $user->login;

        return $name !== '' && $name !== $login
            ? $name . ' (' . $login . ')'
            : $login;
    }
}
