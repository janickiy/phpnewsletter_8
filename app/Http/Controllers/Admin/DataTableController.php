<?php

namespace App\Http\Controllers\Admin;


use App\Helpers\PermissionsHelper;
use App\Helpers\StringHelper;
use App\Models\Category;
use App\Models\Macros;
use App\Models\ReadySent;
use App\Models\Redirect;
use App\Models\Schedule;
use App\Models\Smtp;
use App\Models\Subscribers;
use App\Models\Templates;
use App\Models\User;
use App\Support\ProjectAccess;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class DataTableController extends Controller
{
    /**
     * Return email template rows formatted for the templates DataTable.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getTemplates(): JsonResponse
    {
        $rows = Templates::query()
            ->with('attach')
            ->select('templates.*');

        return DataTables::of($rows)
            ->addColumn('checkbox', fn ($row) => sprintf(
                '<input type="checkbox" class="form-check-input check" value="%d" name="templateId[]">',
                $row->id
            ))
            ->addColumn('action', fn ($row) => sprintf(
                '<div class="btn-group btn-group-sm" role="group"><a title="%s" class="btn btn-outline-primary" href="%s"><i class="fa fa-edit"></i></a></div>',
                __('frontend.str.edit'),
                route('admin.templates.edit', ['id' => $row->id])
            ))
            ->editColumn('name', function ($row) {
                $body = preg_replace('/(<.*?>)|(&.*?;)/', '', $row->body);

                return '<div class="template-cell-title">' . e($row->name) . '</div>' .
                    '<small class="template-cell-excerpt text-muted">' .
                    e(StringHelper::shortText($body ?? '', 500)) .
                    '</small>';
            })
            ->editColumn('prior', function ($row) {
                $class = match ((int) $row->prior) {
                    1 => 'text-bg-danger',
                    2 => 'text-bg-secondary',
                    default => 'text-bg-primary',
                };

                return '<span class="badge ' . $class . '">' . e($row->getPrior()) . '</span>';
            })
            ->addColumn('attach', fn ($row) => $row->attach->count() > 0
                ? '<span class="badge text-bg-success">' . e(__('frontend.str.yes')) . '</span>'
                : '<span class="badge text-bg-secondary">' . e(__('frontend.str.no')) . '</span>')
            ->editColumn('created_at', fn ($row) => $this->formatDateTime($row->created_at))
            ->rawColumns(['action', 'name', 'checkbox', 'prior', 'attach'])
            ->make(true);
    }

    /**
     * Return category rows with subscriber counts and action buttons for DataTables.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getCategory(): JsonResponse
    {
        $rows = Category::query()
            ->selectRaw('categories.id, categories.name, count(subscriptions.category_id) AS subcount')
            ->leftJoin('subscriptions', 'categories.id', '=', 'subscriptions.category_id')
            ->groupBy('categories.id', 'categories.name');

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                $editBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-primary" href="%s"><i class="fas fa-edit"></i></a>',
                    __('frontend.str.edit'),
                    route('admin.category.edit', ['id' => $row->id])
                );

                $deleteBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-danger deleteRow" id="%d"><i class="fas fa-trash"></i></a>',
                    __('frontend.str.remove'),
                    $row->id
                );

                return '<div class="btn-group btn-group-sm" role="group">' . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Return SMTP account rows with status, checkbox, and action columns for DataTables.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getSmtp(): JsonResponse
    {
        $rows = Smtp::query();

        return DataTables::of($rows)
            ->addColumn('checkbox', fn ($row) => sprintf(
                '<input type="checkbox" class="form-check-input check" value="%d" name="activate[]">',
                $row->id
            ))
            ->editColumn('active', fn ($row) => $row->active === 1
                ? '<span class="badge text-bg-success">' . e(__('frontend.str.yes')) . '</span>'
                : '<span class="badge text-bg-secondary">' . e(__('frontend.str.no')) . '</span>')
            ->editColumn('activeStatus', fn ($row) => $row->active)
            ->addColumn('action', function ($row) {
                $editBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-primary" href="%s"><i class="fas fa-edit"></i></a>',
                    __('frontend.str.edit'),
                    route('admin.smtp.edit', ['id' => $row->id])
                );

                $deleteBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-danger deleteRow" id="%d"><i class="fas fa-trash"></i></a>',
                    __('frontend.str.remove'),
                    $row->id
                );

                return '<div class="btn-group btn-group-sm" role="group">' . $editBtn . $deleteBtn . '</div>';
            })
            ->editColumn('created_at', fn ($row) => $this->formatDateTime($row->created_at))
            ->rawColumns(['action', 'checkbox', 'active'])
            ->make(true);
    }

    /**
     * Return subscriber rows with category names, status, and action columns for DataTables.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getSubscribers(): JsonResponse
    {
        $rows = Subscribers::query()
            ->with([
                'projects:id,name',
                'subscriptions:subscriber_id,category_id',
                'subscriptions.category:id,name',
            ])
            ->select([
                'subscribers.id',
                'subscribers.name',
                'subscribers.email',
                'subscribers.active',
                'subscribers.created_at',
            ]);

        if (!ProjectAccess::userIsAdmin()) {
            $projectIds = ProjectAccess::availableProjectIds();

            $rows->whereHas('projects', function ($query) use ($projectIds): void {
                $query->whereIn('projects.id', $projectIds);
            });
        }

        return DataTables::of($rows)
            ->addColumn('checkbox', fn ($row) => sprintf(
                '<input type="checkbox" class="form-check-input check" value="%d" name="activate[]">',
                $row->id
            ))
            ->addColumn('subscriptions', function ($row) {
                $categories = $row->subscriptions
                    ->map(fn ($subscription) => $subscription->category?->name)
                    ->filter()
                    ->unique();

                if ($categories->isEmpty()) {
                    return '<span class="text-muted">-</span>';
                }

                return $categories
                    ->map(fn ($name) => '<span class="badge text-bg-secondary me-1">' . e($name) . '</span>')
                    ->implode('');
            })
            ->addColumn('projects', function ($row) {
                if ($row->projects->isEmpty()) {
                    return '<span class="text-muted">-</span>';
                }

                return $row->projects
                    ->map(fn ($project) => '<span class="badge text-bg-info me-1">' . e($project->name) . '</span>')
                    ->implode('');
            })
            ->editColumn('active', fn ($row) => $row->active === 1
                ? '<span class="badge text-bg-success">' . e(__('frontend.str.yes')) . '</span>'
                : '<span class="badge text-bg-secondary">' . e(__('frontend.str.no')) . '</span>')
            ->editColumn('activeStatus', fn ($row) => $row->active)
            ->addColumn('action', function ($row) {
                $editBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-primary" href="%s"><i class="fa fa-edit"></i></a>',
                    __('frontend.str.edit'),
                    route('admin.subscribers.edit', ['id' => $row->id])
                );

                $deleteBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-danger deleteRow" id="%d"><i class="fa fa-trash"></i></a>',
                    __('frontend.str.remove'),
                    $row->id
                );

                return '<div class="btn-group btn-group-sm" role="group">' . $editBtn . $deleteBtn . '</div>';
            })
            ->editColumn('created_at', fn ($row) => $this->formatDateTime($row->created_at))
            ->rawColumns(['action', 'checkbox', 'subscriptions', 'projects', 'active'])
            ->make(true);
    }

    /**
     * Return admin user rows with role labels and action buttons for DataTables.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getUsers(): JsonResponse
    {
        $rows = User::query();

        return DataTables::of($rows)
            ->addColumn('action', function ($row) {
                $editBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-primary" href="%s"><i class="fas fa-edit"></i></a>',
                    __('frontend.str.edit'),
                    route('admin.users.edit', ['id' => $row->id])
                );

                $deleteBtn = (int) $row->id !== (int) Auth::id()
                    ? sprintf(
                        '<a title="%s" class="btn btn-outline-danger deleteRow" id="%d"><i class="fas fa-trash"></i></a>',
                        __('frontend.str.remove'),
                        $row->id
                    )
                    : '';

                return '<div class="btn-group btn-group-sm" role="group">' . $editBtn . $deleteBtn . '</div>';
            })
            ->editColumn('role', fn ($row) => $row->role_label)
            ->editColumn('created_at', fn ($row) => $this->formatDateTime($row->created_at))
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Return mailing summary rows for the log overview DataTable.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getLogs(): JsonResponse
    {
        $rows = Schedule::query()
            ->selectRaw('schedule.id, schedule.event_start, schedule.event_end, COUNT(ready_sent.id) AS count, SUM(ready_sent.success=1) AS sent, SUM(ready_sent.readMail=1) AS read_mail')
            ->join('ready_sent', 'schedule.id', '=', 'ready_sent.schedule_id')
            ->groupBy('ready_sent.schedule_id', 'schedule.event_start', 'schedule.event_end', 'schedule.id');

        ProjectAccess::scopeReadySentQuery($rows);

        return DataTables::of($rows)
            ->editColumn('count', fn ($row) => sprintf(
                '<a href="%s" class="btn btn-outline-primary btn-sm"><i class="fas fa-list me-1"></i>%d</a>',
                route('admin.log.info', ['id' => $row->id]),
                (int) $row->count
            ))
            ->editColumn('sent', fn ($row) => sprintf(
                '<span class="badge text-bg-success">%d</span>',
                (int) $row->sent
            ))
            ->addColumn('unsent', function ($row) {
                $unsent = (int) $row->count - (int) $row->sent;
                $class = $unsent > 0 ? 'text-bg-danger' : 'text-bg-secondary';

                return '<span class="badge ' . $class . '">' . $unsent . '</span>';
            })
            ->editColumn('read_mail', fn ($row) => sprintf(
                '<span class="badge text-bg-info">%d</span>',
                (int) ($row->read_mail ?? 0)
            ))
            ->addColumn('report', fn ($row) => PermissionsHelper::has_permission('admin')
                ? sprintf(
                    '<a href="%s" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i>%s</a>',
                    route('admin.log.report', ['id' => $row->id]),
                    e(__('frontend.str.download'))
                )
                : '')
            ->editColumn('event_start', fn ($row) => $this->formatDateTime($row->event_start))
            ->editColumn('event_end', fn ($row) => $this->formatDateTime($row->event_end))
            ->rawColumns(['count', 'sent', 'unsent', 'read_mail', 'report'])
            ->make(true);
    }

    /**
     * Return per-recipient delivery log rows, optionally filtered by schedule ID.
     *
     * @param int|null $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function getInfoLog(?int $id = null): JsonResponse
    {
        $rows = $id
            ? ReadySent::query()->where('schedule_id', $id)
            : ReadySent::query();

        ProjectAccess::scopeReadySentQuery($rows);

        return DataTables::of($rows)
            ->editColumn('success', fn ($row) => (int) $row->success === 1
                ? '<span class="badge text-bg-success">' . e(__('frontend.str.send_status_yes')) . '</span>'
                : '<span class="badge text-bg-danger">' . e(__('frontend.str.send_status_no')) . '</span>')
            ->editColumn('readMail', fn ($row) => (int) $row->readMail === 1
                ? '<span class="badge text-bg-info">' . e(__('frontend.str.yes')) . '</span>'
                : '<span class="badge text-bg-secondary">' . e(__('frontend.str.no')) . '</span>')
            ->editColumn('errorMsg', fn ($row) => $row->errorMsg
                ? '<span class="text-danger">' . e($row->errorMsg) . '</span>'
                : '<span class="text-muted">-</span>')
            ->addColumn('status', fn ($row) => (int) $row->success)
            ->addColumn('read', fn ($row) => (int) $row->readMail)
            ->editColumn('created_at', fn ($row) => $this->formatDateTime($row->created_at))
            ->rawColumns(['success', 'readMail', 'errorMsg'])
            ->make(true);
    }

    /**
     * Return grouped redirect tracking rows with report links for DataTables.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getRedirectLogs(): JsonResponse
    {
        $rows = Redirect::query()
            ->selectRaw('url, COUNT(email) as count')
            ->groupBy('url')
            ->distinct();

        return DataTables::of($rows)
            ->editColumn('count', fn ($row) => sprintf(
                '<a href="%s" class="btn btn-outline-primary btn-sm"><i class="fas fa-list me-1"></i>%d</a>',
                route('admin.redirect.info', ['url' => $this->encodeRouteBase64($row->url)]),
                (int) $row->count
            ))
            ->addColumn('report', fn ($row) => PermissionsHelper::has_permission('admin')
                ? sprintf(
                    '<a href="%s" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel me-1"></i>%s</a>',
                    route('admin.redirect.report', ['url' => $this->encodeRouteBase64($row->url)]),
                    e(__('frontend.str.download'))
                )
                : '')
            ->rawColumns(['count', 'report'])
            ->make(true);
    }

    /**
     * Return redirect tracking details for a single encoded URL.
     *
     * @param string $url
     * @return JsonResponse
     * @throws \Exception
     */
    public function getInfoRedirectLog(string $url): JsonResponse
    {
        $decodedUrl = $this->decodeRouteBase64($url);

        $rows = Redirect::query()->where('url', $decodedUrl);

        return DataTables::of($rows)
            ->editColumn('created_at', fn ($row) => $this->formatDateTime($row->created_at))
            ->make(true);
    }

    /**
     * Encode binary-safe base64 for a single route segment.
     *
     * @param string $value
     * @return string
     */
    private function encodeRouteBase64(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    /**
     * Decode URL-safe or regular base64 route parameters.
     *
     * @param string $value
     * @return string
     */
    private function decodeRouteBase64(string $value): string
    {
        $normalized = strtr($value, '-_', '+/');
        $padding = strlen($normalized) % 4;

        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($normalized, true) ?: '';
    }

    /**
     * Return macro rows with action buttons for the macros DataTable.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getMacros(): JsonResponse
    {
        $rows = Macros::query();

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                $editBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-primary" href="%s"><i class="fas fa-edit"></i></a>',
                    __('frontend.str.edit'),
                    route('admin.macros.edit', ['id' => $row->id])
                );

                $deleteBtn = sprintf(
                    '<a title="%s" class="btn btn-outline-danger deleteRow" id="%d"><i class="fas fa-trash"></i></a>',
                    __('frontend.str.remove'),
                    $row->id
                );

                return '<div class="btn-group btn-group-sm" role="group">' . $editBtn . $deleteBtn . '</div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Normalize database or date-like values for display in DataTables.
     *
     * @param mixed $value
     * @return string
     */
    private function formatDateTime(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        $timestamp = strtotime((string) $value);

        return $timestamp !== false
            ? date('Y-m-d H:i:s', $timestamp)
            : (string) $value;
    }

}
