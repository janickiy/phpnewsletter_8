<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Projects\StoreRequest;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Templates;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Show project creation form scoped to an organization.
     */
    public function create(Organization $organization): View
    {
        return view('admin.projects.create_edit', [
            'organization' => $organization,
            'statusOptions' => Project::statusOptions(),
            'templateOptions' => Templates::getOption(),
            'timezoneOptions' => $this->timezoneOptions(),
            'localeOptions' => config('app.languages', []),
            'infoAlert' => __('frontend.hint.projects_create'),
            'title' => __('frontend.title.projects_create'),
        ]);
    }

    /**
     * Persist project under the selected organization.
     */
    public function store(StoreRequest $request, Organization $organization): RedirectResponse
    {
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
     * Show project edit form.
     */
    public function edit(Organization $organization, Project $project): View
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);

        return view('admin.projects.create_edit', [
            'organization' => $organization,
            'row' => $project,
            'statusOptions' => Project::statusOptions(),
            'templateOptions' => Templates::getOption(),
            'timezoneOptions' => $this->timezoneOptions(),
            'localeOptions' => config('app.languages', []),
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
        abort_if((int) $project->organization_id !== (int) $organization->id, 404);
    }

    private function timezoneOptions(): array
    {
        $timezones = timezone_identifiers_list();

        return array_combine($timezones, $timezones);
    }
}
