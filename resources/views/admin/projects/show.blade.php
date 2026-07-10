@extends('admin.app')

@section('title', $title)

@section('breadcrumbs')
    <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard.index') }}">{{ __('frontend.str.admin_panel') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.organizations.show', ['organization' => $organization->id]) }}">{{ $organization->name }}</a>
        </li>
        <li class="breadcrumb-item active">{{ $project->name }}</li>
    </ol>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">

    <style>
        .project-page #projectTemplatesTable {
            width: 100% !important;
        }

        .project-page #projectTemplatesTable th,
        .project-page #projectTemplatesTable td {
            vertical-align: middle;
        }

        .project-page #projectTemplatesTable thead th {
            white-space: nowrap;
        }

        .project-page .templates-bulk-actions {
            max-width: 280px;
        }

        .template-online-log {
            background-color: var(--bs-tertiary-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: var(--bs-border-radius);
            max-height: 260px;
            min-height: 72px;
            overflow: auto;
            padding: .75rem;
        }

        .template-send-stats {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: .5rem .75rem;
        }

        .template-send-controls {
            align-items: center;
            display: flex;
            gap: .75rem;
            margin-top: 1rem;
        }

        .template-send-controls .btn {
            align-items: center;
            aspect-ratio: 1 / 1;
            border-radius: 50% !important;
            display: inline-flex;
            flex: 0 0 3.75rem;
            height: 3.75rem;
            justify-content: center;
            padding: 0;
            width: 3.75rem;
        }

        .template-send-controls .btn i {
            line-height: 1;
        }

        #divStatus {
            display: inline-block;
            min-height: 20px;
        }

        #divStatus.error {
            color: #dc3545;
        }

        #divStatus.success {
            color: #28a745;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid project-page">
        <div class="row g-3">
            <div class="col-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-folder-open me-1"></i>
                            {{ __('frontend.str.project') }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">ID</dt>
                            <dd class="col-sm-9">{{ $project->id }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.project') }}</dt>
                            <dd class="col-sm-9">{{ $project->name }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.organization') }}</dt>
                            <dd class="col-sm-9">
                                <a href="{{ route('admin.organizations.show', ['organization' => $organization->id]) }}">
                                    {{ $organization->name }}
                                </a>
                            </dd>

                            <dt class="col-sm-3">{{ __('frontend.str.status') }}</dt>
                            <dd class="col-sm-9">
                                <span class="badge {{ \App\Enums\ProjectStatus::badgeClassFor($project->status) }}">
                                    {{ $project->status_label }}
                                </span>
                            </dd>

                            <dt class="col-sm-3">{{ __('frontend.str.default_sender_name') }}</dt>
                            <dd class="col-sm-9">{{ $project->default_sender_name ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.default_from_email') }}</dt>
                            <dd class="col-sm-9">{{ $project->default_from_email ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.default_reply_to') }}</dt>
                            <dd class="col-sm-9">{{ $project->default_reply_to ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.timezone') }}</dt>
                            <dd class="col-sm-9">{{ $project->timezone ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.form.description') }}</dt>
                            <dd class="col-sm-9">{{ $project->description ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.added') }}</dt>
                            <dd class="col-sm-9">{{ optional($project->created_at)->format('d.m.Y H:i') ?: '-' }}</dd>
                        </dl>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row gap-2 justify-content-start">
                        <a class="btn btn-primary" href="{{ route('admin.projects.edit', ['organization' => $organization->id, 'project' => $project->id]) }}">
                            <i class="fas fa-edit me-1"></i>
                            {{ __('frontend.form.edit') }}
                        </a>

                        <a class="btn btn-secondary btn-back" href="{{ route('admin.organizations.show', ['organization' => $organization->id]) }}">
                            {{ __('frontend.form.back') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card card-outline card-secondary h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-shield me-1"></i>
                            {{ __('frontend.str.project_admin') }}
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.name') }}</th>
                                    <th>{{ __('frontend.str.login') }}</th>
                                    @if($canManageProjectRoles)
                                        <th class="text-end">{{ __('frontend.str.action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($project->administrators as $administrator)
                                    <tr>
                                        <td>{{ $administrator->name ?: '-' }}</td>
                                        <td>{{ $administrator->login }}</td>
                                        @if($canManageProjectRoles)
                                            <td class="text-end">
                                                <form action="{{ route('admin.projects.administrators.destroy', ['organization' => $organization->id, 'project' => $project->id, 'user' => $administrator->id]) }}"
                                                      method="post"
                                                      class="d-inline"
                                                      onsubmit="return confirm('{{ __('frontend.str.confirm_remove') }}');">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('frontend.str.remove') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $canManageProjectRoles ? 3 : 2 }}" class="text-center text-muted py-4">
                                            {{ __('frontend.str.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($canManageProjectRoles && $projectAdministratorOptions !== [])
                        <div class="card-footer">
                            <form action="{{ route('admin.projects.administrators.store', ['organization' => $organization->id, 'project' => $project->id]) }}"
                                  method="post"
                                  class="row g-2 align-items-end">
                                @csrf

                                <div class="col-md-8">
                                    {!! form_label('user_id', __('frontend.str.add_project_administrator'), ['class' => 'form-label']) !!}
                                    {!! form_select('user_id', $projectAdministratorOptions, old('user_id'), [
                                        'placeholder' => __('frontend.form.select'),
                                        'class' => 'form-select js-live-search-select' . ($errors->has('user_id') ? ' is-invalid' : ''),
                                        'data-search-placeholder' => __('frontend.form.search'),
                                        'data-no-results' => __('pagination.s_zero_records'),
                                        'required' => true,
                                    ]) !!}

                                    @if ($errors->has('user_id'))
                                        <div class="invalid-feedback">{{ $errors->first('user_id') }}</div>
                                    @endif
                                </div>

                                <div class="col-md-auto">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('frontend.form.add') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card card-outline card-secondary h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-check me-1"></i>
                            {{ __('frontend.str.moderator') }}
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.name') }}</th>
                                    <th>{{ __('frontend.str.login') }}</th>
                                    @if($canManageProjectRoles)
                                        <th class="text-end">{{ __('frontend.str.action') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($project->moderators as $moderator)
                                    <tr>
                                        <td>{{ $moderator->name ?: '-' }}</td>
                                        <td>{{ $moderator->login }}</td>
                                        @if($canManageProjectRoles)
                                            <td class="text-end">
                                                <form action="{{ route('admin.projects.moderators.destroy', ['organization' => $organization->id, 'project' => $project->id, 'user' => $moderator->id]) }}"
                                                      method="post"
                                                      class="d-inline"
                                                      onsubmit="return confirm('{{ __('frontend.str.confirm_remove') }}');">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('frontend.str.remove') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $canManageProjectRoles ? 3 : 2 }}" class="text-center text-muted py-4">
                                            {{ __('frontend.str.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($canManageProjectRoles && $projectModeratorOptions !== [])
                        <div class="card-footer">
                            <form action="{{ route('admin.projects.moderators.store', ['organization' => $organization->id, 'project' => $project->id]) }}"
                                  method="post"
                                  class="row g-2 align-items-end">
                                @csrf

                                <div class="col-md-8">
                                    {!! form_label('user_id', __('frontend.str.add_moderator'), ['class' => 'form-label']) !!}
                                    {!! form_select('user_id', $projectModeratorOptions, old('user_id'), [
                                        'placeholder' => __('frontend.form.select'),
                                        'class' => 'form-select js-live-search-select' . ($errors->has('user_id') ? ' is-invalid' : ''),
                                        'data-search-placeholder' => __('frontend.form.search'),
                                        'data-no-results' => __('pagination.s_zero_records'),
                                        'required' => true,
                                    ]) !!}

                                    @if ($errors->has('user_id'))
                                        <div class="invalid-feedback">{{ $errors->first('user_id') }}</div>
                                    @endif
                                </div>

                                <div class="col-md-auto">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('frontend.form.add') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-envelope-open-text me-1"></i>
                            {{ __('frontend.menu.templates') }}
                        </h3>

                        <div class="card-tools">
                            <a href="{{ route('admin.projects.templates.create', ['organization' => $organization->id, 'project' => $project->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('frontend.str.add_template') }}
                            </a>
                        </div>
                    </div>

                    {!! form_open(['url' => route('admin.templates.status'), 'method' => 'post', 'id' => 'projectTemplatesForm']) !!}
                    {!! form_hidden('project_id', $project->id) !!}

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="projectTemplatesTable" class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 48px">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               title="{{ __('frontend.str.check_uncheck_all') }}"
                                               id="checkAll">
                                    </th>
                                    <th style="width: 72px">ID</th>
                                    <th>{{ __('frontend.str.template') }}</th>
                                    <th>{{ __('frontend.str.importance') }}</th>
                                    <th>{{ __('frontend.str.attachments') }}</th>
                                    <th>{{ __('frontend.str.date') }}</th>
                                    <th class="text-end">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($templates as $template)
                                    @php
                                        $priorityClass = match ((int) $template->prior) {
                                            1 => 'text-bg-danger',
                                            2 => 'text-bg-secondary',
                                            default => 'text-bg-primary',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input check" value="{{ $template->id }}" name="templateId[]">
                                        </td>
                                        <td>{{ $template->id }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $template->name }}</div>
                                            <small class="text-muted d-block mt-1">{{ $template->excerpt() }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $priorityClass }}">{{ $template->getPrior() }}</span>
                                        </td>
                                        <td>
                                            @if($template->attach_count > 0)
                                                <span class="badge text-bg-success">{{ __('frontend.str.yes') }}</span>
                                            @else
                                                <span class="badge text-bg-secondary">{{ __('frontend.str.no') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($template->created_at)->format('d.m.Y H:i') ?: '-' }}</td>
                                        <td class="text-end">
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.templates.edit', ['id' => $template->id]) }}" title="{{ __('frontend.form.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-body-tertiary">
                        <div class="input-group input-group-sm templates-bulk-actions">
                            <span class="input-group-text">
                                <i class="fas fa-list-check"></i>
                            </span>

                            {!! form_select('action', [
                                '0' => __('frontend.str.send'),
                                '1' => __('frontend.str.remove'),
                            ], null, ['class' => 'form-select', 'id' => 'select_action', 'placeholder' => '--' . __('frontend.str.action') . '--'], [0 => ['data-id' => 'sendmail', 'class' => 'open_modal']]) !!}

                            {!! form_submit(__('frontend.str.apply'), ['class' => 'btn btn-success', 'disabled' => '', 'id' => 'apply']) !!}
                        </div>
                    </div>

                    {!! form_close() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade template-send-modal" id="modal-lg">
        <input id="logId" type="hidden" value="0">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-paper-plane me-1"></i>
                        {{ __('frontend.str.online_newsletter_log') }}
                        <span id="process"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        {!! form_label('categoryId', __('frontend.form.subscribers_category'), ['class' => 'form-label']) !!}
                        {!! form_select('categoryId[]', $categoryOptions, null, ['id' => 'categoryId', 'multiple' => 'multiple', 'placeholder' => __('frontend.form.select_category'), 'class' => 'form-select custom-scroll', 'style' => 'width: 100%']) !!}
                    </div>

                    <div id="onlinelog" class="template-online-log mb-3"></div>

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span><span id="leftsend">0</span>% {{ __('frontend.str.left') }}</span>
                        <span id="timer2">00:00:00</span>
                    </div>

                    <div class="progress progress-sm progress-bar-striped progress-bar-animated mb-3">
                        <div class="progress-bar bg-dark" role="progressbar" style="width: 0%"></div>
                    </div>

                    <div class="online_statistics">
                        <div class="template-send-stats">
                            <span class="badge text-bg-secondary">
                                {{ __('frontend.str.total') }}: <span id="totalsendlog">0</span>
                            </span>
                            <span class="badge text-bg-success">
                                {{ __('frontend.str.good') }}: <span id="successful">0</span>
                            </span>
                            <span class="badge text-bg-danger">
                                {{ __('frontend.str.bad') }}: <span id="unsuccessful">0</span>
                            </span>
                        </div>

                        <div class="mt-3">
                            <span id="divStatus"></span>
                        </div>

                        <div class="template-send-controls">
                            <button id="sendout" class="btn btn-success rounded-circle btn-lg"
                                    title="{{ __('frontend.str.send_out_newsletter') }}">
                                <i class="fa fa-play"></i>
                            </button>
                            <button id="stopsendout"
                                    class="btn btn-danger rounded-circle btn-lg disabled" disabled="disabled"
                                    title="{{ __('frontend.str.stop_newsletter') }}">
                                <i class="fa fa-stop"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="{{ asset('vendor/datatables/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

    <script>
        const ajaxUrl = '{{ route('admin.ajax.action') }}';
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const serverErrorText = "{{ __('frontend.str.error_server') }}";
        const noNewsletterSelectedText = "{{ __('frontend.str.no_newsletter_selected') }}";
        const noCategorySelectedText = "{{ __('frontend.form.select_category') }}";

        let mailingState = {
            paused: false,
            completed: true,
            countTimer: null,
            logTimer: null,
            sendRequest: null,
        };

        $(function () {
            const modalElement = document.getElementById('modal-lg');
            const modalInstance = new bootstrap.Modal(modalElement, {});

            $('#sendout').on('click', function () {
                resetStatusMessage();

                const templateIds = getSelectedTemplateIds();
                const categoryIds = getSelectedCategoryIds();

                if (templateIds.length === 0) {
                    showStatusMessage(noNewsletterSelectedText);
                    return;
                }

                if (categoryIds.length === 0) {
                    showStatusMessage(noCategorySelectedText);
                    return;
                }

                startMailing(templateIds, categoryIds);
            });

            $('#stopsendout').on('click', function () {
                stopMailing();
            });

            $('#apply').on('click', function (event) {
                const actionId = $('#select_action').val();

                if (actionId === '') {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Error',
                        text: "{{ __('frontend.str.select_action') }}",
                        type: 'error',
                        showCancelButton: false,
                        cancelButtonText: "{{ __('frontend.str.cancel') }}",
                        confirmButtonColor: '#DD6B55',
                        closeOnConfirm: false
                    });

                    return;
                }

                if (actionId == 1) {
                    event.preventDefault();
                    const form = $(this).parents('form');

                    Swal.fire({
                        title: "{{ __('frontend.str.delete_confirmation') }}",
                        text: "{{ __('frontend.str.confirm_remove') }}",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: "{{ __('frontend.str.yes') }}",
                        cancelButtonText: "{{ __('frontend.str.cancel') }}",
                        closeOnConfirm: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });

                    return;
                }

                if (actionId == 0) {
                    event.preventDefault();
                    resetModalState();
                    modalInstance.show();
                }
            });

            $('#checkAll').on('click change', function () {
                $('#projectTemplatesTable').find('input.check').prop('checked', this.checked);
                countChecked();
            });

            $('#projectTemplatesTable').on('change', 'input.check', function () {
                syncCheckAllState();
                countChecked();
            });

            $('#projectTemplatesTable').DataTable({
                "oLanguage": {
                    "sLengthMenu": "{{ __('pagination.s_length_menu') }}",
                    "sZeroRecords": "{{ __('pagination.s_zero_records') }}",
                    "sInfo": "{{ __('pagination.s_info') }}",
                    "sInfoEmpty": "{{ __('pagination.s_info_empty') }}",
                    "sInfoFiltered": "{{ __('pagination.s_infofiltered') }}",
                    "oPaginate": {
                        "sFirst": "{{ __('pagination.s_paginate.first') }}",
                        "sLast": "{{ __('pagination.s_paginate.last') }}",
                        "sNext": "{{ __('pagination.s_paginate.next') }}",
                        "sPrevious": "{{ __('pagination.s_paginate.previous') }}"
                    },
                    "sSearch": ' <i class="fas fa-search" aria-hidden="true"></i>'
                },
                aaSorting: [[1, 'asc']],
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    {targets: 0, className: 'text-center', width: '48px', orderable: false, searchable: false},
                    {targets: 1, width: '72px'},
                    {targets: [3, 4, 5, 6], className: 'text-nowrap'},
                    {targets: 6, orderable: false, searchable: false, className: 'text-end text-nowrap'}
                ],
                drawCallback: function () {
                    syncCheckAllState();
                    countChecked();
                }
            });

            $(modalElement).on('hidden.bs.modal', function () {
                if (!mailingState.completed && !mailingState.paused) {
                    stopMailing(true);
                    return;
                }

                resetModalState();
            });
        });

        function getSelectedTemplateIds() {
            return $('#projectTemplatesTable').find('input.check:checked').map(function () {
                const value = parseInt($(this).val(), 10);
                return Number.isInteger(value) ? value : null;
            }).get();
        }

        function getSelectedCategoryIds() {
            const values = $('#categoryId').val() || [];

            return values.map(function (value) {
                return parseInt(value, 10);
            }).filter(function (value) {
                return Number.isInteger(value);
            });
        }

        function countChecked() {
            $('#apply').prop('disabled', getSelectedTemplateIds().length === 0);
        }

        function syncCheckAllState() {
            const total = $('#projectTemplatesTable').find('input.check').length;
            const checked = $('#projectTemplatesTable').find('input.check:checked').length;
            $('#checkAll').prop('checked', total > 0 && total === checked);
        }

        function startMailing(templateIds, categoryIds) {
            mailingState.paused = false;
            mailingState.completed = false;
            clearTimers();
            resetCounters();
            resetStatusMessage();
            setRunningUiState();

            $.ajax({
                url: ajaxUrl,
                method: 'POST',
                headers: {'X-CSRF-TOKEN': csrfToken},
                data: {
                    action: 'start_mailing',
                },
                dataType: 'json'
            }).done(function (data) {
                if (data.result === true && data.logId) {
                    $('#logId').val(data.logId);
                    startPolling();
                    sendOut(templateIds, categoryIds);
                    return;
                }

                failProcess(data.errors || serverErrorText);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                failProcess(extractErrorMessage(jqXHR, textStatus, errorThrown));
            });
        }

        function startPolling() {
            getCountProcess();
            onlineLogProcess();

            mailingState.countTimer = setInterval(function () {
                if (!mailingState.completed) {
                    getCountProcess();
                }
            }, 2000);

            mailingState.logTimer = setInterval(function () {
                if (!mailingState.completed) {
                    onlineLogProcess();
                }
            }, 2000);
        }

        function sendOut(templateIds, categoryIds) {
            mailingState.sendRequest = $.ajax({
                type: 'POST',
                url: ajaxUrl,
                headers: {'X-CSRF-TOKEN': csrfToken},
                data: {
                    action: 'send_out',
                    categoryId: categoryIds,
                    templateId: templateIds,
                    logId: $('#logId').val(),
                },
                cache: false,
                dataType: 'json',
                timeout: 10000,
            }).done(function (json) {
                if (json.result !== true) {
                    failProcess(json.errors || serverErrorText);
                    return;
                }

                if (json.completed === true) {
                    completeProcess();
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (textStatus === 'abort') {
                    return;
                }

                failProcess(extractErrorMessage(jqXHR, textStatus, errorThrown));
            }).always(function () {
                mailingState.sendRequest = null;
            });
        }

        function getCountProcess() {
            const logId = parseInt($('#logId').val(), 10);

            if (!logId || mailingState.completed) {
                return;
            }

            $.ajax({
                url: ajaxUrl,
                cache: false,
                method: 'POST',
                headers: {'X-CSRF-TOKEN': csrfToken},
                data: {
                    action: 'count_send',
                    logId: logId,
                    categoryId: getSelectedCategoryIds(),
                },
                dataType: 'json',
                success: function (json) {
                    if (json.result !== true) {
                        if (json.errors) {
                            failProcess(json.errors);
                        }
                        return;
                    }

                    $('#totalsendlog').text(json.total ?? 0);
                    $('#unsuccessful').text(json.unsuccessful ?? 0);
                    $('#successful').text(json.success ?? 0);
                    $('#timer2').text(json.time ?? '00:00:00');

                    const leftsend = Number(json.leftsend ?? 0);
                    $('.progress-bar').css('width', leftsend + '%');
                    $('#leftsend').text(leftsend);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    failProcess(extractErrorMessage(jqXHR, textStatus, errorThrown));
                },
            });
        }

        function onlineLogProcess() {
            if (mailingState.completed) {
                return;
            }

            $.ajax({
                type: 'POST',
                cache: false,
                url: ajaxUrl,
                headers: {'X-CSRF-TOKEN': csrfToken},
                data: {
                    action: 'log_online',
                },
                dataType: 'json',
                success: function (data) {
                    if (!Array.isArray(data.item)) {
                        return;
                    }

                    const html = data.item
                        .filter(function (item) {
                            return item && typeof item.email !== 'undefined' && item.email !== null && item.email !== '';
                        })
                        .map(function (item) {
                            return escapeHtml(item.email) + ' - ' + escapeHtml(item.status ?? '');
                        })
                        .join('<br>');

                    $('#onlinelog').html(html);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    failProcess(extractErrorMessage(jqXHR, textStatus, errorThrown));
                },
            });
        }

        function stopMailing(silent = false) {
            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                headers: {'X-CSRF-TOKEN': csrfToken},
                data: {
                    action: 'process',
                    command: 'stop',
                },
                dataType: 'json',
                success: function (data) {
                    if (data.result !== true) {
                        failProcess(data.errors || serverErrorText);
                        return;
                    }

                    completeProcess();

                    if (!silent) {
                        showSuccessMessage('Stopped');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    failProcess(extractErrorMessage(jqXHR, textStatus, errorThrown));
                },
            });
        }

        function completeProcess() {
            mailingState.completed = true;
            mailingState.paused = false;
            clearTimers();

            if (mailingState.sendRequest) {
                mailingState.sendRequest.abort();
                mailingState.sendRequest = null;
            }

            setIdleUiState();
            $('#timer2').text('00:00:00');
            $('#leftsend').text(100);
            $('.progress-bar').css('width', '100%');
        }

        function failProcess(message) {
            completeProcess();
            showStatusMessage(message || serverErrorText);
        }

        function clearTimers() {
            if (mailingState.countTimer) {
                clearInterval(mailingState.countTimer);
                mailingState.countTimer = null;
            }

            if (mailingState.logTimer) {
                clearInterval(mailingState.logTimer);
                mailingState.logTimer = null;
            }
        }

        function resetModalState() {
            clearTimers();
            mailingState.paused = false;
            mailingState.completed = true;

            if (mailingState.sendRequest) {
                mailingState.sendRequest.abort();
                mailingState.sendRequest = null;
            }

            $('#logId').val(0);
            $('#onlinelog').empty();
            resetCounters();
            resetStatusMessage();
            setIdleUiState();
            $('#timer2').text('00:00:00');
            $('#leftsend').text(0);
            $('.progress-bar').css('width', '0%');
        }

        function resetCounters() {
            $('#totalsendlog').text(0);
            $('#successful').text(0);
            $('#unsuccessful').text(0);
        }

        function setRunningUiState() {
            $('#stopsendout').removeClass('disabled').prop('disabled', false);
            $('#sendout').addClass('disabled').prop('disabled', true);
            $('#process').removeClass().addClass('showprocess');
        }

        function setIdleUiState() {
            $('#stopsendout').addClass('disabled').prop('disabled', true);
            $('#sendout').removeClass('disabled').prop('disabled', false);
            $('#process').removeClass();
        }

        function showStatusMessage(message) {
            $('#divStatus')
                .removeClass('success')
                .addClass('error')
                .html(escapeHtml(message));
        }

        function showSuccessMessage(message) {
            $('#divStatus')
                .removeClass('error')
                .addClass('success')
                .html(escapeHtml(message));
        }

        function resetStatusMessage() {
            $('#divStatus')
                .removeClass('error success')
                .empty();
        }

        function extractErrorMessage(jqXHR, textStatus, errorThrown) {
            const responseJson = jqXHR.responseJSON || {};
            return responseJson.errors || errorThrown || textStatus || serverErrorText;
        }

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }
    </script>
@endsection
