<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Organizations\StoreRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Show organizations and their project counts.
     */
    public function index(): View
    {
        return view('admin.organizations.index', [
            'organizations' => Organization::query()
                ->withCount('projects')
                ->orderBy('name')
                ->paginate(20),
            'infoAlert' => __('frontend.hint.organizations_index'),
            'title' => __('frontend.title.organizations_index'),
        ]);
    }

    /**
     * Show organization creation form.
     */
    public function create(): View
    {
        return view('admin.organizations.create_edit', [
            'infoAlert' => __('frontend.hint.organizations_create'),
            'title' => __('frontend.title.organizations_create'),
        ]);
    }

    /**
     * Persist a new organization.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $organization = Organization::query()->create($request->validated());
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
     * Show organization details and its projects.
     */
    public function show(Organization $organization): View
    {
        $organization->load([
            'projects' => fn ($query) => $query
                ->with('unsubscribeTemplate:id,name')
                ->orderBy('name'),
        ]);

        return view('admin.organizations.show', [
            'organization' => $organization,
            'infoAlert' => __('frontend.hint.organizations_show'),
            'title' => $organization->name,
        ]);
    }

    /**
     * Show organization edit form.
     */
    public function edit(Organization $organization): View
    {
        return view('admin.organizations.create_edit', [
            'row' => $organization,
            'infoAlert' => __('frontend.hint.organizations_edit'),
            'title' => __('frontend.title.organizations_edit'),
        ]);
    }

    /**
     * Save organization changes.
     */
    public function update(StoreRequest $request, Organization $organization): RedirectResponse
    {
        try {
            $organization->update($request->validated());
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
     * Delete organization and its projects.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        try {
            $organization->delete();
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.organizations.index')
            ->with('success', __('frontend.msg.data_successfully_deleted'));
    }
}
