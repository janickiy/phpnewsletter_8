<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Requests\Admin\Organizations\StoreRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Show organizations and their project counts.
     */
    public function index(): View
    {
        return view('admin.organizations.index', [
            'organizations' => $this->organizationsQueryForCurrentUser()
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
            $organization = Organization::query()->create($this->organizationCreateData($request));
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

        $organization->load([
            'owner:id,name,login',
            'administrators:id,name,login,role',
            'projects' => fn ($query) => $query->orderBy('name'),
        ]);

        return view('admin.organizations.show', [
            'organization' => $organization,
            'administratorOptions' => $this->administratorOptions($organization),
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
            'ownerOptions' => $this->ownerOptions(),
            'infoAlert' => __('frontend.hint.organizations_edit'),
            'title' => __('frontend.title.organizations_edit'),
        ]);
    }

    /**
     * Save organization changes.
     */
    public function update(StoreRequest $request, Organization $organization): RedirectResponse
    {
        $this->ensureOrganizationAvailable($organization);

        try {
            $organization->update($this->organizationData($request));
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
            $organization->delete();
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.organizations.index')
            ->with('success', __('frontend.msg.data_successfully_deleted'));
    }

    public function storeAdministrator(Request $request, Organization $organization): RedirectResponse
    {
        $this->ensureOrganizationAvailable($organization);

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists(User::getTableName(), 'id')->where(function ($query): void {
                    $query->whereIn('role', [
                        UserRole::Admin->value,
                        UserRole::OrganizationAdmin->value,
                    ]);
                }),
            ],
        ]);

        if ((int) $validated['user_id'] !== (int) $organization->owner_id) {
            $organization->administrators()->syncWithoutDetaching([(int) $validated['user_id']]);
        }

        return to_route('admin.organizations.show', ['organization' => $organization->id])
            ->with('success', __('message.information_successfully_added'));
    }

    public function destroyAdministrator(Organization $organization, User $user): RedirectResponse
    {
        $this->ensureOrganizationAvailable($organization);

        $organization->administrators()->detach($user->id);

        return to_route('admin.organizations.show', ['organization' => $organization->id])
            ->with('success', __('frontend.msg.data_successfully_deleted'));
    }

    private function ownerOptions(bool $onlyCurrentUser = false): array
    {
        $query = User::query();

        if ($onlyCurrentUser || ! $this->currentUserIsAdmin()) {
            $query->whereKey(Auth::id());
        }

        return $query
            ->orderBy('name')
            ->orderBy('login')
            ->get(['id', 'name', 'login'])
            ->mapWithKeys(function (User $user): array {
                $name = trim((string) $user->name);
                $login = (string) $user->login;
                $label = $name !== '' && $name !== $login
                    ? $name . ' (' . $login . ')'
                    : $login;

                return [$user->id => $label];
            })
            ->toArray();
    }

    private function organizationsQueryForCurrentUser(): Builder
    {
        $query = Organization::query();

        if (! $this->currentUserIsAdmin()) {
            $query->where(function (Builder $query): void {
                $query
                    ->where('owner_id', Auth::id())
                    ->orWhereHas('administrators', function (Builder $query): void {
                        $query->whereKey(Auth::id());
                    });
            });
        }

        return $query;
    }

    private function organizationData(StoreRequest $request): array
    {
        $data = $request->validated();

        if (! $this->currentUserIsAdmin()) {
            $data['owner_id'] = Auth::id();
        }

        return $data;
    }

    private function organizationCreateData(StoreRequest $request): array
    {
        $data = $request->validated();
        $data['owner_id'] = Auth::id();

        return $data;
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

    private function administratorOptions(Organization $organization): array
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
            ->mapWithKeys(function (User $user): array {
                $name = trim((string) $user->name);
                $login = (string) $user->login;
                $label = $name !== '' && $name !== $login
                    ? $name . ' (' . $login . ')'
                    : $login;

                return [$user->id => $label];
            })
            ->toArray();
    }
}
