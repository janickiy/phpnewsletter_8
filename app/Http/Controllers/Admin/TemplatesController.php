<?php

namespace App\Http\Controllers\Admin;


use App\Enums\UserRole;
use App\Http\Requests\Admin\Templates\StoreRequest;
use App\Http\Requests\Admin\Templates\DeleteRequest;
use App\Http\Requests\Admin\Templates\UpdateRequest;
use App\Models\Macros;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Templates;
use App\Repositories\CategoryRepository;
use App\Repositories\TemplateRepository;
use App\Services\TemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class TemplatesController extends Controller
{
    /**
     * Inject repositories and services required to manage email templates and attachments.
     *
     * @param TemplateRepository $templateRepository
     * @param CategoryRepository $categoryRepository
     * @param TemplateService $templateService
     */
    public function __construct(
        private readonly TemplateRepository $templateRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TemplateService $templateService,
    ) {
        parent::__construct();
    }

    /**
     * Show the template management page with category filters.
     *
     * @return View
     */
    public function index(): View
    {
        return view('admin.templates.index', [
            'categoryOptions' => $this->categoryRepository->getOption(),
            'infoAlert' => __('frontend.hint.template_index'),
            'title' => __('frontend.title.template_index'),
        ]);
    }

    /**
     * Show the form used to create a new email template.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.templates.create_edit', [
            'infoAlert' => __('frontend.hint.template_create'),
            'macrosList' => $this->getMacros(),
            'projectOptions' => $this->projectOptions(),
            'title' => __('frontend.title.template_create'),
        ]);
    }

    /**
     * Show the template creation form scoped to a project.
     *
     * @param Organization $organization
     * @param Project $project
     * @return View
     */
    public function createForProject(Organization $organization, Project $project): View
    {
        $this->ensureProjectBelongsToOrganization($organization, $project);

        return view('admin.templates.create_edit', [
            'organization' => $organization,
            'project' => $project,
            'infoAlert' => __('frontend.hint.template_create'),
            'macrosList' => $this->getMacros(),
            'projectOptions' => $this->projectOptions(),
            'title' => __('frontend.title.template_create'),
        ]);
    }

    /**
     * Validate and persist a new email template together with uploaded attachments.
     *
     * @param StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $project = Project::query()->findOrFail($request->integer('project_id'));
        $this->ensureProjectAvailable($project);

        try {
            $template = $this->templateRepository->create($request->validated());

            $this->templateService->storeAttach($request, $template->id);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return to_route('admin.projects.show', [
            'organization' => $project->organization_id,
            'project' => $project->id,
        ])->with('success', __('message.information_successfully_added'));
    }

    /**
     * Show the edit form for an existing email template and its attachments.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $template = $this->templateRepository->find($id);

        abort_if(!$template, 404);
        $template->load('project.organization');
        $this->ensureTemplateAvailable($template);

        return view('admin.templates.create_edit', [
            'template' => $template,
            'organization' => $template->project?->organization,
            'project' => $template->project,
            'attachment' => $template->attach,
            'infoAlert' => __('frontend.hint.template_edit'),
            'macrosList' => $this->getMacros(),
            'projectOptions' => $this->projectOptions(),
            'title' => __('frontend.title.template_edit'),
        ]);
    }

    /**
     * Validate and save changes to an email template and its attachments.
     *
     * @param UpdateRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request): RedirectResponse
    {
        $template = $this->templateRepository->find((int) $request->id);
        abort_if(!$template, 404);

        $template->load('project.organization');
        $this->ensureTemplateAvailable($template);

        try {
            $data = $request->safe()->except(['id']);
            $data['project_id'] = $template->project_id;

            $this->templateRepository->updateWithMapping(
                (int) $request->id,
                $data
            );

            $this->templateService->storeAttach($request, (int) $request->id);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return to_route('admin.projects.show', [
            'organization' => $template->project->organization_id,
            'project' => $template->project_id,
        ])->with('success', __('message.data_updated'));
    }

    /**
     * Delete an email template and related resources.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->templateRepository->remove($id);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.templates.index')->with('success', __('frontend.msg.data_successfully_deleted'));
    }

    /**
     * Delete selected email templates from the bulk action form.
     *
     * @param DeleteRequest $request
     * @return RedirectResponse
     */
    public function delete(DeleteRequest $request): RedirectResponse
    {
        try {
            $this->templateRepository->updateStatus(
                $request->templateId,
                (int) $request->action
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', $e->getMessage());
        }

        return to_route('admin.templates.index')->with('success', __('message.actions_completed'));
    }

    /**
     * Build the human-readable macro reference string displayed in the template editor.
     *
     * @return string
     */
    private function getMacros(): string
    {
        return Macros::query()
            ->get()
            ->map(fn ($macro) => '{{' . $macro->name . '}} - ' . $macro->getType())
            ->implode(', ');
    }

    /**
     * @return array<int, string>
     */
    private function projectOptions(): array
    {
        return $this->availableProjectsQuery()
            ->get()
            ->mapWithKeys(fn (Project $project) => [
                $project->id => $project->organization->name . ' / ' . $project->name,
            ])
            ->all();
    }

    private function availableProjectsQuery()
    {
        $query = Project::query()
            ->with('organization:id,name,owner_id')
            ->orderBy('name');

        if (Auth::user()?->role !== UserRole::Admin->value) {
            $query->whereHas('organization', function ($organizationQuery) {
                $organizationQuery
                    ->where('owner_id', Auth::id())
                    ->orWhereHas('administrators', fn ($administratorQuery) => $administratorQuery->whereKey(Auth::id()));
            });
        }

        return $query;
    }

    private function ensureTemplateAvailable(Templates $template): void
    {
        abort_unless($template->project, 404);

        $this->ensureProjectAvailable($template->project);
    }

    private function ensureProjectBelongsToOrganization(Organization $organization, Project $project): void
    {
        abort_if((int) $project->organization_id !== (int) $organization->id, 404);

        $this->ensureProjectAvailable($project);
    }

    private function ensureProjectAvailable(Project $project): void
    {
        $project->loadMissing('organization');
        $organization = $project->organization;

        abort_unless(
            Auth::user()?->role === UserRole::Admin->value
                || (int) $organization->owner_id === (int) Auth::id()
                || $organization->administrators()->whereKey(Auth::id())->exists(),
            404
        );
    }
}
