@extends('admin.app')

@section('title', $title)

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
    </style>
@endsection

@section('content')

    @php
        $statusClasses = [
            \App\Models\Project::STATUS_ACTIVE => 'text-bg-success',
            \App\Models\Project::STATUS_ARCHIVED => 'text-bg-secondary',
            \App\Models\Project::STATUS_BLOCKED => 'text-bg-danger',
        ];
    @endphp

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
                                <span class="badge {{ $statusClasses[$project->status] ?? 'text-bg-secondary' }}">
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

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="projectTemplatesTable" class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
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
        $(function () {
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
                aaSorting: [[0, 'asc']],
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    {targets: 0, width: '72px'},
                    {targets: [2, 3, 4, 5], className: 'text-nowrap'},
                    {targets: 5, orderable: false, searchable: false, className: 'text-end text-nowrap'}
                ]
            });
        });
    </script>
@endsection
