<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Requests\Admin\Organizations\StoreAdministratorRequest;
use App\Http\Requests\Admin\Organizations\StoreRequest;
use App\Http\Requests\Admin\Organizations\UpdateRequest;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Inject the organization repository used by organization CRUD actions.
     */
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
    ) {
        parent::__construct();
    }

    /**
     * Show organizations and their project counts.
     */
    public function index(): View
    {
        return view('admin.organizations.index', [
            'organizations' => $this->organizationRepository
                ->queryForUser(Auth::user())
                ->with('owner:id,name,login')
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
            $organization = $this->organizationRepository->createFromData(
                $request->toDto((int) Auth::id())
            );
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
        $this->ensureOrganizationAvailable($organization);

        $organization = $this->organizationRepository->loadDetails($organization);

        return view('admin.organizations.show', [
            'organization' => $organization,
            'administratorOptions' => $this->organizationRepository->administratorOptions($organization),
            'infoAlert' => __('frontend.hint.organizations_show'),
            'title' => $organization->name,
        ]);
    }

    /**
     * Show organization edit form.
     */
    public function edit(Organization $organization): View
    {
        $this->ensureOrganizationAvailable($organization);

        return view('admin.organizations.create_edit', [
            'row' => $organization,
            'ownerOptions' => $this->organizationRepository->ownerOptions(Auth::user()),
            'infoAlert' => __('frontend.hint.organizations_edit'),
            'title' => __('frontend.title.organizations_edit'),
        ]);
    }

    /**
     * Save organization changes.
     */
    public function update(UpdateRequest $request, Organization $organization): RedirectResponse
    {
        $this->ensureOrganizationAvailable($organization);

        try {
            $ownerId = $this->currentUserIsAdmin()
                ? (int) ($request->input('owner_id') ?: $organization->owner_id)
                : (int) Auth::id();

            $this->organizationRepository->updateFromData(
                $organization->id,
                $request->toDto($ownerId)
            );
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
        $this->ensureOrganizationAvailable($organization);

        try {
            $this->organizationRepository->delete($organization->id);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.organizations.index')
            ->with('success', __('frontend.msg.data_successfully_deleted'));
    }

    public function storeAdministrator(StoreAdministratorRequest $request, Organization $organization): RedirectResponse
    {
        $this->ensureOrganizationAvailable($organization);

        $this->organizationRepository->attachAdministrator(
            $organization,
            $request->integer('user_id')
        );

        return to_route('admin.organizations.show', ['organization' => $organization->id])
            ->with('success', __('message.information_successfully_added'));
    }

    public function destroyAdministrator(Organization $organization, User $user): RedirectResponse
    {
        $this->ensureOrganizationAvailable($organization);

        $this->organizationRepository->detachAdministrator($organization, $user);

        return to_route('admin.organizations.show', ['organization' => $organization->id])
            ->with('success', __('frontend.msg.data_successfully_deleted'));
    }

    private function ensureOrganizationAvailable(Organization $organization): void
    {
        abort_unless(
            $this->currentUserIsAdmin()
                || (int) $organization->owner_id === (int) Auth::id()
                || $organization->administrators()->whereKey(Auth::id())->exists(),
            404
        );
    }

    private function currentUserIsAdmin(): bool
    {
        return Auth::user()?->role === UserRole::Admin->value;
    }
}
