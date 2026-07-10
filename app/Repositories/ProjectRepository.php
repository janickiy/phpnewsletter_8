<?php

namespace App\Repositories;

use App\DTO\ProjectCreateData;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProjectRepository extends BaseRepository
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function createFromData(ProjectCreateData $data): Project
    {
        return $this->model->newQuery()->create($data->toArray());
    }

    public function updateFromData(int $id, ProjectCreateData $data): bool
    {
        return $this->update($id, $data->toArray());
    }

    public function belongsToOrganization(Project $project, Organization $organization): bool
    {
        return (int) $project->organization_id === (int) $organization->id;
    }

    public function loadDetails(Project $project): Project
    {
        return $project->load([
            'administrators:id,name,login,role',
            'moderators:id,name,login,role',
            'projectUsers:id',
        ]);
    }

    public function templatesForProject(Project $project): Collection
    {
        return $project
            ->templates()
            ->withCount('attach')
            ->orderBy('name')
            ->get();
    }

    public function attachProjectUser(Project $project, int $userId, UserRole $role): void
    {
        $project->projectUsers()->syncWithoutDetaching([
            $userId => ['role' => $role->value],
        ]);
    }

    public function detachProjectUser(Project $project, User $user, UserRole $role): void
    {
        DB::table('project_admins')
            ->where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->where('role', $role->value)
            ->delete();
    }

    public function roleOptions(Project $project, UserRole $role): array
    {
        $attachedIds = $project->projectUsers
            ->pluck('id')
            ->filter()
            ->unique()
            ->all();

        return User::query()
            ->where('role', $role->value)
            ->when($attachedIds !== [], fn (Builder $query) => $query->whereNotIn('id', $attachedIds))
            ->orderBy('name')
            ->orderBy('login')
            ->get(['id', 'name', 'login'])
            ->mapWithKeys(fn (User $user): array => [$user->id => $this->userOptionLabel($user)])
            ->all();
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
