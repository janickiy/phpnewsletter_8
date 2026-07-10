<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Requests\Admin\Projects\StoreRequest;
use App\Models\Category;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Templates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Show project creation form scoped to an organization.
     */
    public function create(Organization $organization): View
    {
        $this->ensureOrganizationAvailable($organization);

        return view('admin.projects.create_edit', [
            'organization' => $organization,
            'statusOptions' => Project::statusOptions(),
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
        $this->ensureOrganizationAvailable($organization);

        try {
            $organization->projects()->create($request->validated());
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

        return view('admin.projects.show', [
            'organization' => $organization,
            'project' => $project,
            'templates' => Templates::query()
                ->where('project_id', $project->id)
                ->withCount('attach')
                ->orderBy('name')
                ->get(),
            'categoryOptions' => Category::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'title' => $project->name,
        ]);
    }

    /**
     * Show project edit form.
     */
    public function edit(Organization $organization, Project $project): View
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);

        return view('admin.projects.create_edit', [
            'organization' => $organization,
            'row' => $project,
            'statusOptions' => Project::statusOptions(),
            'timezoneOptions' => $this->timezoneOptions(),
            'infoAlert' => __('frontend.hint.projects_edit'),
            'title' => __('frontend.title.projects_edit'),
        ]);
    }

    /**
     * Save project changes.
     */
    public function update(StoreRequest $request, Organization $organization, Project $project): RedirectResponse
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);

        try {
            $project->update($request->validated());
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

        try {
            $project->delete();
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.organizations.show', ['organization' => $organization->id])
            ->with('success', __('frontend.msg.data_successfully_deleted'));
    }

    private function ensureProjectBelongsToOrganization(Organization $organization, Project $project): void
    {
        $this->ensureOrganizationAvailable($organization);

        abort_if((int) $project->organization_id !== (int) $organization->id, 404);
    }

    private function ensureOrganizationAvailable(Organization $organization): void
    {
        abort_unless(
            Auth::user()?->role === UserRole::Admin->value
                || (int) $organization->owner_id === (int) Auth::id()
                || $organization->administrators()->whereKey(Auth::id())->exists(),
            404
        );
    }

    private function timezoneOptions(): array
    {
        $timezones = timezone_identifiers_list();

        return array_combine($timezones, $timezones);
    }
}
