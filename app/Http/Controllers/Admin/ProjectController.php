<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\ProjectStatus;
use App\Http\Requests\Admin\Projects\StoreAdministratorRequest as StoreProjectAdministratorRequest;
use App\Http\Requests\Admin\Projects\StoreModeratorRequest;
use App\Http\Requests\Admin\Projects\StoreRequest;
use App\Http\Requests\Admin\Projects\UpdateRequest;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\ProjectRepository;
use App\Support\ProjectAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Inject repositories used by project management actions.
     */
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
        parent::__construct();
    }

    /**
     * Show projects assigned to the current moderator.
     */
    public function index(): View
    {
        abort_unless(Auth::user()?->role === UserRole::Moderator->value, 403);

        return view('admin.projects.index', [
            'projects' => ProjectAccess::availableProjectsQuery()
                ->withCount(['templates', 'subscribers'])
                ->paginate(20),
            'infoAlert' => __('frontend.hint.projects_index'),
            'title' => __('frontend.title.projects_index'),
        ]);
    }

    /**
     * Show project creation form scoped to an organization.
     */
    public function create(Organization $organization): View
    {
        $this->ensureCanManageProject($organization);

        return view('admin.projects.create_edit', [
            'organization' => $organization,
            'statusOptions' => ProjectStatus::options(),
            'timezoneOptions' => $this->timezoneOptions(),
            'infoAlert' => __('frontend.hint.projects_create'),
            'title' => __('frontend.title.projects_create'),
        ]);
    }

    /**
     * Persist project under the selected organization.
     */
    public function store(StoreRequest $request, Organization $organization): RedirectResponse
    {
        $this->ensureCanManageProject($organization);

        try {
            $this->projectRepository->createFromData($request->toDto($organization->id));
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return to_route('admin.organizations.show', ['organization' => $organization->id])
            ->with('success', __('message.information_successfully_added'));
    }

    /**
     * Show project details.
     */
    public function show(Organization $organization, Project $project): View
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);

        $project = $this->projectRepository->loadDetails($project);

        $canManageProject = $this->canManageProject($organization);
        $canManageProjectRoles = $this->canManageProjectUsers($organization);

        return view('admin.projects.show', [
            'organization' => $organization,
            'project' => $project,
            'templates' => $this->projectRepository->templatesForProject($project),
            'categoryOptions' => $this->categoryRepository->getOption(),
            'canManageProject' => $canManageProject,
            'canManageProjectRoles' => $canManageProjectRoles,
            'backUrl' => $this->projectBackUrl($organization, $canManageProject),
            'projectAdministratorOptions' => $canManageProjectRoles
                ? $this->projectRepository->roleOptions($project, UserRole::ProjectAdmin)
                : [],
            'projectModeratorOptions' => $canManageProjectRoles
                ? $this->projectRepository->roleOptions($project, UserRole::Moderator)
                : [],
            'title' => $project->name,
        ]);
    }

    /**
     * Show project edit form.
     */
    public function edit(Organization $organization, Project $project): View
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);
        $this->ensureCanManageProject($organization);

        return view('admin.projects.create_edit', [
            'organization' => $organization,
            'row' => $project,
            'statusOptions' => ProjectStatus::options(),
            'timezoneOptions' => $this->timezoneOptions(),
            'infoAlert' => __('frontend.hint.projects_edit'),
            'title' => __('frontend.title.projects_edit'),
        ]);
    }

    /**
     * Save project changes.
     */
    public function update(UpdateRequest $request, Organization $organization, Project $project): RedirectResponse
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);
        $this->ensureCanManageProject($organization);

        try {
            $this->projectRepository->updateFromData($project->id, $request->toDto($organization->id));
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return to_route('admin.organizations.show', ['organization' => $organization->id])
            ->with('success', __('message.data_updated'));
    }

    /**
     * Delete project from organization.
     */
    public function destroy(Organization $organization, Project $project): RedirectResponse
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);
        $this->ensureCanManageProject($organization);

        try {
            $this->projectRepository->delete($project->id);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.organizations.show', ['organization' => $organization->id])
            ->with('success', __('frontend.msg.data_successfully_deleted'));
    }

    public function storeAdministrator(StoreProjectAdministratorRequest $request, Organization $organization, Project $project): RedirectResponse
    {
        return $this->storeProjectUser($request->integer('user_id'), $organization, $project, UserRole::ProjectAdmin);
    }

    public function destroyAdministrator(Organization $organization, Project $project, User $user): RedirectResponse
    {
        return $this->destroyProjectUser($organization, $project, $user, UserRole::ProjectAdmin);
    }

    public function storeModerator(StoreModeratorRequest $request, Organization $organization, Project $project): RedirectResponse
    {
        return $this->storeProjectUser($request->integer('user_id'), $organization, $project, UserRole::Moderator);
    }

    public function destroyModerator(Organization $organization, Project $project, User $user): RedirectResponse
    {
        return $this->destroyProjectUser($organization, $project, $user, UserRole::Moderator);
    }

    private function ensureProjectBelongsToOrganization(Organization $organization, Project $project): void
    {
        abort_unless($this->projectRepository->belongsToOrganization($project, $organization), 404);
        abort_unless($this->canViewProject($organization, $project), 404);
    }

    private function ensureCanManageProject(Organization $organization): void
    {
        abort_unless($this->canManageProject($organization), 403);
    }

    private function canViewProject(Organization $organization, Project $project): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $this->canManageProject($organization)
            || ProjectAccess::availableProjectsQuery($user)->whereKey($project->id)->exists();
    }

    private function canManageProject(Organization $organization): bool
    {
        $user = Auth::user();

        if (!$user || $user->role === UserRole::Moderator->value) {
            return false;
        }

        return $user->role === UserRole::Admin->value
            || (int) $organization->owner_id === (int) $user->id
            || $organization->administrators()->whereKey($user->id)->exists();
    }

    private function projectBackUrl(Organization $organization, bool $canManageProject): string
    {
        if ($canManageProject) {
            return route('admin.organizations.show', ['organization' => $organization->id]);
        }

        if (Auth::user()?->role === UserRole::Moderator->value) {
            return route('admin.projects.index');
        }

        return route('admin.dashboard.index');
    }

    private function storeProjectUser(int $userId, Organization $organization, Project $project, UserRole $role): RedirectResponse
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);
        $this->ensureCanManageProjectUsers($organization);

        $this->projectRepository->attachProjectUser($project, $userId, $role);

        return to_route('admin.projects.show', ['organization' => $organization->id, 'project' => $project->id])
            ->with('success', __('message.information_successfully_added'));
    }

    private function destroyProjectUser(Organization $organization, Project $project, User $user, UserRole $role): RedirectResponse
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);
        $this->ensureCanManageProjectUsers($organization);

        $this->projectRepository->detachProjectUser($project, $user, $role);

        return to_route('admin.projects.show', ['organization' => $organization->id, 'project' => $project->id])
            ->with('success', __('frontend.msg.data_successfully_deleted'));
    }

    private function ensureCanManageProjectUsers(Organization $organization): void
    {
        abort_unless($this->canManageProjectUsers($organization), 403);
    }

    private function canManageProjectUsers(Organization $organization): bool
    {
        return $this->canManageProject($organization);
    }

    private function timezoneOptions(): array
    {
        $timezones = timezone_identifiers_list();

        return array_combine($timezones, $timezones);
    }
}
