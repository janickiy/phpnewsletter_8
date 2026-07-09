@extends('admin.app')

@section('title', $title)

@section('css')
    <style>
        .dashboard-small-box {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 154px;
            margin-bottom: 0;
            overflow: hidden;
            width: 100%;
        }

        .dashboard-small-box > .inner {
            flex: 1 1 auto;
            min-height: 118px;
            padding: 1rem;
        }

        .dashboard-small-box h3 {
            letter-spacing: 0;
            line-height: 1;
        }

        .dashboard-small-box p {
            margin-bottom: 0;
            max-width: calc(100% - 4.5rem);
        }

        .dashboard-small-box .dashboard-note {
            display: block;
            color: currentColor;
            font-size: .875rem;
            margin-top: .35rem;
            min-height: 18px;
            opacity: .82;
        }

        .dashboard-small-box .small-box-footer {
            font-weight: 600;
            margin-top: auto;
        }

        .dashboard-summary-row > [class*="col"],
        .dashboard-equal-row > [class*="col"] {
            display: flex;
        }

        .dashboard-card .card-title {
            align-items: center;
            display: flex;
            float: none;
            gap: .5rem;
            font-weight: 600;
        }

        .dashboard-card {
            margin-bottom: 0;
            width: 100%;
        }

        .dashboard-card > .card-body {
            min-width: 0;
        }

        .dashboard-action-link {
            align-items: center;
            display: flex;
            gap: .65rem;
        }

        .dashboard-action-icon {
            flex: 0 0 1.5rem;
            text-align: center;
        }

        .dashboard-action-arrow {
            margin-left: auto;
        }

        .dashboard-table td,
        .dashboard-table th {
            vertical-align: middle;
        }

        .dashboard-empty {
            color: var(--bs-secondary-color);
            padding: 1.5rem 0;
            text-align: center;
        }

        .dashboard-progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: .35rem;
        }

        @media (max-width: 575.98px) {
            .dashboard-small-box p {
                max-width: none;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $summaryBoxes = [
            [
                'theme' => 'info',
                'linkClass' => 'link-dark',
                'url' => route('admin.templates.index'),
                'icon' => 'fas fa-envelope-open-text',
                'value' => number_format($stats['templates']),
                'label' => __('frontend.menu.templates'),
                'note' => __('frontend.str.add_template'),
            ],
            [
                'theme' => 'success',
                'linkClass' => 'link-light',
                'url' => route('admin.subscribers.index'),
                'icon' => 'fas fa-user-friends',
                'value' => number_format($stats['subscribers']),
                'label' => __('frontend.menu.subscribers'),
                'note' => __('frontend.dashboard.active_count', ['count' => number_format($stats['activeSubscribers'])]),
            ],
            [
                'theme' => 'warning',
                'linkClass' => 'link-dark',
                'url' => route('admin.schedule.index'),
                'icon' => 'fas fa-calendar-alt',
                'value' => number_format($stats['schedule']),
                'label' => __('frontend.menu.schedule'),
                'note' => __('frontend.dashboard.active_count', ['count' => number_format($stats['upcomingSchedule'])]),
            ],
            [
                'theme' => 'danger',
                'linkClass' => 'link-light',
                'url' => route('admin.log.index'),
                'icon' => 'fas fa-paper-plane',
                'value' => number_format($stats['sentTotal']),
                'label' => __('frontend.str.sent'),
                'note' => number_format($stats['sentFailed']) . ' ' . __('frontend.str.error'),
            ],
            [
                'theme' => 'primary',
                'linkClass' => 'link-light',
                'url' => route('admin.category.index'),
                'icon' => 'fas fa-list',
                'value' => number_format($stats['categories']),
                'label' => __('frontend.menu.subscribers_category'),
                'note' => __('frontend.str.category'),
            ],
            [
                'theme' => 'secondary',
                'linkClass' => 'link-light',
                'url' => route('admin.smtp.index'),
                'icon' => 'fas fa-inbox',
                'value' => number_format($stats['smtp']),
                'label' => __('frontend.str.smtp_server'),
                'note' => __('frontend.dashboard.active_count', ['count' => number_format($stats['activeSmtp'])]),
            ],
            [
                'theme' => 'dark',
                'linkClass' => 'link-light',
                'url' => route('admin.redirect.index'),
                'icon' => 'fas fa-mouse-pointer',
                'value' => number_format($stats['clicks']),
                'label' => __('frontend.str.redirect'),
                'note' => __('frontend.str.redirect_number'),
            ],
            [
                'theme' => 'light',
                'linkClass' => 'link-dark',
                'url' => route('admin.users.index'),
                'icon' => 'fas fa-users-cog',
                'value' => number_format($stats['users']),
                'label' => __('frontend.menu.users'),
                'note' => number_format($stats['macros']) . ' ' . __('frontend.menu.macros'),
            ],
        ];
    @endphp

    <div class="container-fluid dashboard-page">
        <div class="row g-3 mb-3 dashboard-summary-row">
            @foreach($summaryBoxes as $box)
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="small-box text-bg-{{ $box['theme'] }} dashboard-small-box">
                        <div class="inner">
                            <h3>{{ $box['value'] }}</h3>
                            <p>
                                {{ $box['label'] }}
                                <span class="dashboard-note">{{ $box['note'] }}</span>
                            </p>
                        </div>
                        <i class="small-box-icon {{ $box['icon'] }}"></i>
                        <a href="{{ $box['url'] }}" class="small-box-footer {{ $box['linkClass'] }} link-underline-opacity-0 link-underline-opacity-50-hover">
                            {{ __('frontend.dashboard.open_section') }} <i class="fas fa-arrow-circle-right ms-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mb-3 dashboard-equal-row">
            <div class="col-12 col-xl-4">
                <div class="card card-outline card-primary dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt text-primary"></i>
                            {{ __('frontend.dashboard.quick_actions') }}
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.templates.create') }}" class="list-group-item list-group-item-action dashboard-action-link">
                                <i class="dashboard-action-icon fas fa-plus text-info"></i>
                                <span>{{ __('frontend.str.add_template') }}</span>
                                <i class="dashboard-action-arrow fas fa-angle-right"></i>
                            </a>
                            @if(PermissionsHelper::has_permission('admin|moderator'))
                                <a href="{{ route('admin.subscribers.import') }}" class="list-group-item list-group-item-action dashboard-action-link">
                                    <i class="dashboard-action-icon fas fa-file-import text-success"></i>
                                    <span>{{ __('frontend.str.import_subscribers') }}</span>
                                    <i class="dashboard-action-arrow fas fa-angle-right"></i>
                                </a>
                            @endif
                            <a href="{{ route('admin.schedule.create') }}" class="list-group-item list-group-item-action dashboard-action-link">
                                <i class="dashboard-action-icon fas fa-calendar-plus text-warning"></i>
                                <span>{{ __('frontend.str.add_schedule') }}</span>
                                <i class="dashboard-action-arrow fas fa-angle-right"></i>
                            </a>
                            @if(PermissionsHelper::has_permission('admin'))
                                <a href="{{ route('admin.settings.index') }}" class="list-group-item list-group-item-action dashboard-action-link">
                                    <i class="dashboard-action-icon fas fa-cogs text-secondary"></i>
                                    <span>{{ __('frontend.menu.settings') }}</span>
                                    <i class="dashboard-action-arrow fas fa-angle-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12 col-xl-8">
                <div class="card card-outline card-info dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-paper-plane text-info"></i>
                            {{ __('frontend.str.newsletter') }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.log.index') }}" class="btn btn-tool" title="{{ __('frontend.menu.mailing_log') }}">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle dashboard-table mb-0">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.newsletter') }}</th>
                                    <th>{{ __('frontend.str.total') }}</th>
                                    <th>{{ __('frontend.str.sent') }}</th>
                                    <th>{{ __('frontend.str.read') }}</th>
                                    <th>{{ __('frontend.str.date') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($latestMailings as $mailing)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.log.info', ['id' => $mailing->id]) }}">
                                                {{ $mailing->event_name }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($mailing->count) }}</td>
                                        <td>
                                            <span class="badge text-bg-success">{{ number_format($mailing->sent ?? 0) }}</span>
                                            <span class="badge text-bg-danger">{{ number_format($mailing->failed ?? 0) }}</span>
                                        </td>
                                        <td>{{ number_format($mailing->read_mail ?? 0) }}</td>
                                        <td>{{ optional(\Illuminate\Support\Carbon::parse($mailing->last_sent_at))->format('d.m.Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="dashboard-empty">{{ __('frontend.dashboard.no_mailings') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row g-3 mb-3 dashboard-equal-row">
            <div class="col-12 col-xl-4">
                <div class="card card-outline card-success dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line text-success"></i>
                            {{ __('frontend.dashboard.delivery_overview') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="dashboard-progress-label">
                            <span>{{ __('frontend.str.sent') }}</span>
                            <strong>{{ $stats['deliveryRate'] }}%</strong>
                        </div>
                        <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $stats['deliveryRate'] }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success" style="width: {{ $stats['deliveryRate'] }}%"></div>
                        </div>

                        <div class="dashboard-progress-label">
                            <span>{{ __('frontend.str.read') }}</span>
                            <strong>{{ $stats['openRate'] }}%</strong>
                        </div>
                        <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $stats['openRate'] }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-info" style="width: {{ $stats['openRate'] }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>{{ __('frontend.str.good') }}</span>
                            <strong>{{ number_format($stats['sentSuccess']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('frontend.str.bad') }}</span>
                            <strong>{{ number_format($stats['sentFailed']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('frontend.str.read') }}</span>
                            <strong>{{ number_format($stats['readTotal']) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8">
                <div class="card card-outline card-primary dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-envelope-open-text text-primary"></i>
                            {{ __('frontend.menu.templates') }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.templates.index') }}" class="btn btn-tool" title="{{ __('frontend.menu.templates') }}">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle dashboard-table mb-0">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.template') }}</th>
                                    <th>{{ __('frontend.str.importance') }}</th>
                                    <th>{{ __('frontend.str.date') }}</th>
                                    <th class="text-end">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($latestTemplates as $template)
                                    <tr>
                                        <td>{{ $template->name }}</td>
                                        <td>{{ $template->getPrior() }}</td>
                                        <td>{{ optional($template->created_at)->format('d.m.Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.templates.edit', ['id' => $template->id]) }}" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-edit me-1"></i>
                                                {{ __('frontend.str.edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="dashboard-empty">{{ __('frontend.dashboard.no_templates') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 dashboard-equal-row">
            <div class="col-12 col-xl-6">
                <div class="card card-outline card-success dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-friends text-success"></i>
                            {{ __('frontend.menu.subscribers') }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.subscribers.index') }}" class="btn btn-tool" title="{{ __('frontend.menu.subscribers') }}">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle dashboard-table mb-0">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.name') }}</th>
                                    <th>{{ __('frontend.str.email') }}</th>
                                    <th>{{ __('frontend.str.status') }}</th>
                                    <th>{{ __('frontend.str.added') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($latestSubscribers as $subscriber)
                                    <tr>
                                        <td>{{ $subscriber->name ?: '-' }}</td>
                                        <td>{{ $subscriber->email }}</td>
                                        <td>
                                            @if($subscriber->active)
                                                <span class="badge text-bg-success">{{ __('frontend.str.activate') }}</span>
                                            @else
                                                <span class="badge text-bg-secondary">{{ __('frontend.str.deactivate') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($subscriber->created_at)->format('d.m.Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="dashboard-empty">{{ __('frontend.dashboard.no_subscribers') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card card-outline card-warning dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt text-warning"></i>
                            {{ __('frontend.menu.schedule') }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.schedule.index') }}" class="btn btn-tool" title="{{ __('frontend.menu.schedule') }}">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle dashboard-table mb-0">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.newsletter') }}</th>
                                    <th>{{ __('frontend.str.template') }}</th>
                                    <th>{{ __('frontend.str.date') }}</th>
                                    <th class="text-end">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($upcomingSchedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->event_name }}</td>
                                        <td>{{ $schedule->template?->name ?: '-' }}</td>
                                        <td>{{ optional(\Illuminate\Support\Carbon::parse($schedule->event_start))->format('d.m.Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.schedule.edit', ['id' => $schedule->id]) }}" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-edit me-1"></i>
                                                {{ __('frontend.str.edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="dashboard-empty">{{ __('frontend.dashboard.no_schedules') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
