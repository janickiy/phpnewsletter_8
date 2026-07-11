<?php

namespace App\Http\Controllers\Admin;


use App\Enums\UserRole;
use App\Helpers\StringHelper;
use App\Http\Requests\Admin\Subscribers\EditRequest;
use App\Http\Requests\Admin\Subscribers\ImportRequest;
use App\Http\Requests\Admin\Subscribers\StoreRequest;
use App\Models\Charsets;
use App\Models\Project;
use App\Models\Subscribers;
use App\Repositories\CategoryRepository;
use App\Repositories\SubscriberRepository;
use App\Repositories\SubscriptionRepository;
use App\Services\DownloadService;
use App\Services\SubscriberService;
use App\Support\ProjectAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;


class SubscribersController extends Controller
{
    /**
     * Inject repositories and services required to manage subscribers and their categories.
     */
    public function __construct(
        private readonly SubscriberRepository $subscribersRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly DownloadService $downloadService,
        private readonly SubscriberService $subscriberService,
    ) {
        parent::__construct();
    }

    /**
     * Show the subscriber management page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('admin.subscribers.index', [
            'canRemoveAllSubscribers' => $this->currentUserIsAdmin(),
            'infoAlert' => __('frontend.hint.subscribers_index'),
            'title' => __('frontend.title.subscribers_index'),
        ]);
    }

    /**
     * Show the form used to create a new subscriber and assign categories.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.subscribers.create_edit', [
            'options' => $this->categoryRepository->getOption(),
            'projectGroups' => ProjectAccess::optionGroups(),
            'infoAlert' => __('frontend.hint.subscribers_create'),
            'title' => __('frontend.title.subscribers_create'),
        ]);
    }

    public function createForProject(Project $project): View
    {
        $this->ensureProjectAvailable($project);
        $project->loadMissing('organization:id,name');

        return view('admin.subscribers.create_edit', [
            'options' => $this->categoryRepository->getOption(),
            'projectGroups' => [
                [
                    'label' => null,
                    'projects' => [
                        [
                            'id' => $project->id,
                            'name' => $project->name,
                        ],
                    ],
                ],
            ],
            'subscriberProjectIds' => [$project->id],
            'lockedProject' => $project,
            'formUrl' => route('admin.projects.subscribers.store', ['project' => $project->id]),
            'backUrl' => route('admin.projects.moderator.show', ['project' => $project->id]),
            'infoAlert' => __('frontend.hint.subscribers_create'),
            'title' => __('frontend.title.subscribers_create'),
        ]);
    }

    /**
     * Validate and persist a new active subscriber with a generated token.
     *
     * @param StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $this->subscribersRepository->add([
                ...$request->validated(),
                'timeSent' => now(),
                'active' => 1,
                'token' => StringHelper::token(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return to_route('admin.subscribers.index')->with('success', __('message.information_successfully_added'));
    }

    public function storeForProject(StoreRequest $request, Project $project): RedirectResponse
    {
        $this->ensureProjectAvailable($project);

        try {
            $this->subscribersRepository->add([
                ...$request->validated(),
                'projectId' => [$project->id],
                'timeSent' => now(),
                'active' => 1,
                'token' => StringHelper::token(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return to_route('admin.projects.moderator.show', ['project' => $project->id])
            ->with('success', __('message.information_successfully_added'));
    }

    /**
     * Show the edit form for an existing subscriber and its category assignments.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $this->ensureSubscriberAvailable($id);

        $row = $this->subscribersRepository->find($id);


        //dd($this->subscribersRepository->getSubscriberCategoryIdList($id));

        abort_if(!$row, 404);

        return view('admin.subscribers.create_edit', [
            'options' => $this->categoryRepository->getOption(),
            'projectGroups' => ProjectAccess::optionGroups(),
            'row' => $row,
            'subscriberCategoryIds' => $this->subscribersRepository->getSubscriberCategoryIdList($id),
            'subscriberProjectIds' => $row->projects()->pluck('projects.id')->toArray(),
            'infoAlert' => __('frontend.hint.subscribers_edit'),
            'title' => __('frontend.title.subscribers_edit'),
        ]);
    }

    /**
     * Validate and save subscriber data together with category assignments.
     *
     * @param EditRequest $request
     * @return RedirectResponse
     */
    public function update(EditRequest $request): RedirectResponse
    {
        $this->ensureSubscriberAvailable((int) $request->id);

        try {
            DB::transaction(function () use ($request) {
                $this->subscribersRepository->update(
                    (int) $request->id,
                    $request->safe()->except(['id', 'categoryId', 'projectId'])
                );

                $this->subscriptionRepository->updateSubscriptions(
                    (array) $request->input('categoryId', []),
                    (int) $request->id
                );

                $this->subscribersRepository->syncProjects(
                    (int) $request->id,
                    (array) $request->input('projectId', [])
                );
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return to_route('admin.subscribers.index')
            ->with('success', __('message.data_updated'));
    }

    /**
     * Delete a subscriber and remove its category subscriptions in one transaction.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->ensureSubscriberAvailable($id);

        DB::transaction(function () use ($id) {
            $this->subscriptionRepository->removeBySubscriberId($id);
            $this->subscribersRepository->delete($id);
        });
    }

    /**
     * Show the subscriber import form with charset and category options.
     *
     * @return View
     */
    public function import(): View
    {
        return view('admin.subscribers.import', [
            'charsets' => Charsets::getOption(),
            'category_options' => $this->categoryRepository->getOption(),
            'maxUploadFileSize' => StringHelper::maxUploadFileSize(),
            'infoAlert' => __('frontend.hint.subscribers_import'),
            'title' => __('frontend.title.subscribers_import'),
        ]);
    }

    /**
     * Import subscribers from an uploaded spreadsheet or text file.
     *
     * @param ImportRequest $request
     * @return StreamedResponse
     */
    public function importSubscribers(ImportRequest $request): StreamedResponse
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $extension = strtolower($request->file('import')->getClientOriginalExtension());

        return response()->stream(function () use ($request, $extension): void {
            $redirectUrl = route('admin.subscribers.index');
            $flush = function (?int $count = null): void {
                $message = $count === null
                    ? 'Import started...'
                    : 'Imported: ' . $count;

                echo '<script>document.getElementById("import-status").textContent = '
                    . json_encode($message)
                    . ';</script>' . str_repeat(' ', 4096);

                if (ob_get_level() > 0) {
                    @ob_flush();
                }

                flush();
            };

            echo '<!doctype html><html><head><meta charset="utf-8"><title>Import</title></head>'
                . '<body><p id="import-status">Import started...</p>';
            $flush();

            try {
                $result = match ($extension) {
                    'csv', 'xls', 'xlsx', 'ods' => $this->subscriberService->importFromExcel($request, $flush),
                    default => $this->subscriberService->importFromText($request, $flush),
                };

                if ($result === false) {
                    session()->flash('error', __('message.error_import_file'));
                } else {
                    session()->flash('success', __('message.import_completed') . $result);
                }
            } catch (\Throwable $e) {
                report($e);

                session()->flash('error', $e->getMessage());
            }

            echo '<script>window.location.href = ' . json_encode($redirectUrl) . ';</script>'
                . '</body></html>';
        }, 200, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Accel-Buffering' => 'no',
        ]);

    }

    /**
     * Show the subscriber export form with category filters.
     *
     * @return View
     */
    public function export(): View
    {
        return view('admin.subscribers.export', [
            'options' => $this->categoryRepository->getOption(),
            'infoAlert' => __('frontend.hint.subscribers_export'),
            'title' => __('frontend.title.subscribers_export'),
        ]);
    }

    /**
     * Stream a subscriber export file based on the selected filters.
     *
     * @param Request $request
     * @return Response|StreamedResponse
     */
    public function exportSubscribers(Request $request): Response|StreamedResponse
    {
        return $this->downloadService->exportSubscribers($request);
    }

    /**
     * Remove all subscribers and subscriptions from the system.
     *
     * @return RedirectResponse
     */
    public function removeAll(): RedirectResponse
    {
        abort_unless($this->currentUserIsAdmin(), 403);

        try {
            DB::transaction(function () {
                $this->subscriptionRepository->deleteAll();
                $this->subscribersRepository->deleteAll();
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.subscribers.index')->with('success', __('message.data_successfully_deleted'));
    }

    /**
     * Apply bulk activation or deactivation to selected subscribers.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        try {
            $subscriberIds = $this->filterAvailableSubscriberIds((array) $request->activate);

            $this->subscribersRepository->updateStatus(
                (int) $request->action,
                $subscriberIds
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.subscribers.index')->with('success', __('message.actions_completed'));
    }

    private function ensureSubscriberAvailable(int $id): void
    {
        if ($this->currentUserIsAdmin()) {
            return;
        }

        abort_unless(
            in_array($id, $this->filterAvailableSubscriberIds([$id]), true),
            403
        );
    }

    private function ensureProjectAvailable(Project $project): void
    {
        abort_unless(
            ProjectAccess::availableProjectsQuery()->whereKey($project->id)->exists(),
            404
        );
    }

    private function filterAvailableSubscriberIds(array $ids): array
    {
        $ids = collect($ids)
            ->filter(static fn ($id): bool => is_numeric($id))
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($ids === [] || $this->currentUserIsAdmin()) {
            return $ids;
        }

        $projectIds = ProjectAccess::availableProjectIds();

        if ($projectIds === []) {
            return [];
        }

        return Subscribers::query()
            ->whereIn('id', $ids)
            ->whereHas('projects', fn ($query) => $query->whereIn('projects.id', $projectIds))
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    private function currentUserIsAdmin(): bool
    {
        return Auth::user()?->role === UserRole::Admin->value;
    }
}
