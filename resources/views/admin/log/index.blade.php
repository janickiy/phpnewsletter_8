@extends('admin.app')

@section('title', $title)

@section('breadcrumbs')
    <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard.index') }}">{{ __('frontend.str.admin_panel') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ $title }}</li>
    </ol>
@endsection

@section('css')

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">

    <style>
        .log-page #itemList,
        .log-page #logList {
            width: 100% !important;
        }

        .log-page #itemList th,
        .log-page #itemList td,
        .log-page #logList th,
        .log-page #logList td {
            vertical-align: middle;
        }

        .log-page #itemList thead th,
        .log-page #logList thead th {
            white-space: nowrap;
        }

        .log-page .log-clear-action {
            min-width: 0;
        }

        .log-page .log-error-cell {
            max-width: 360px;
            white-space: normal;
        }

        .log-page .log-id-cell {
            max-width: 72px;
            min-width: 64px;
            white-space: nowrap;
            width: 72px !important;
        }

        .log-page .log-time-cell {
            max-width: 170px;
            min-width: 150px;
            white-space: nowrap;
            width: 170px !important;
        }

        .log-page .card-body.p-0 .table-responsive {
            padding: 1rem;
        }

        .log-page .card-body.p-0 .table-responsive > .dt-container {
            width: 100%;
        }

        .log-page .card-body.p-0 .table-responsive > .dt-container > .row {
            margin-left: 0;
            margin-right: 0;
        }

        .log-page .card-body.p-0 .table-responsive > .dt-container > .row:first-child {
            align-items: center;
            border-bottom: 1px solid var(--bs-border-color);
            padding-bottom: .75rem;
            padding-top: 0;
        }

        .log-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child {
            align-items: center;
            border-top: 1px solid var(--bs-border-color);
            justify-content: center;
            margin-bottom: 0;
            margin-top: 0 !important;
            padding-bottom: 0;
            padding-top: 1rem;
            row-gap: .75rem;
        }

        .log-page .card-body.p-0 table.dataTable {
            margin-bottom: .75rem !important;
            margin-top: .75rem !important;
        }

        .log-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child > .dt-layout-start {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: left;
        }

        .log-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child > .dt-layout-end {
            display: flex;
            flex: 0 0 100%;
            justify-content: center;
            margin-left: 0 !important;
            max-width: 100%;
        }

        .log-page .dt-length,
        .log-page .dt-search,
        .log-page .dt-info,
        .log-page .dt-paging {
            padding: 0;
        }

        .log-page .dt-search {
            align-items: center;
            display: flex;
            gap: .5rem;
            justify-content: flex-end;
        }

        .log-page .dt-search label {
            margin-bottom: 0;
        }

        .log-page .dt-search input {
            margin-left: 0;
        }

        .log-page .dt-paging .pagination {
            justify-content: center;
            margin-bottom: 0;
        }
    </style>

@endsection

@section('content')

    <div class="container-fluid log-page">
        <div class="row g-3">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-area me-1"></i>
                            {{ __('frontend.menu.log') }}
                        </h3>

                        @if(PermissionsHelper::has_permission('admin'))
                            <div class="card-tools d-flex align-items-center gap-2">
                                <button id="clearLogButton"
                                   type="button"
                                   class="btn btn-outline-danger btn-sm log-clear-action"
                                   onclick="confirmation(event)"
                                   title="{{ __('frontend.str.log_clear') }}">
                                    <i class="fas fa-trash me-1"></i>
                                    {{ __('frontend.str.log_clear') }}
                                </button>

                                <span id="clearLogSpinner" class="d-none">
                                    <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="itemList" class="table table-striped table-hover mb-0 align-middle nowrap">
                                <thead>
                                <tr>
                                    <th class="log-id-cell text-center">ID</th>
                                    <th class="log-time-cell">{{ __('frontend.str.time') }}</th>
                                    <th class="text-center">{{ __('frontend.str.total') }}</th>
                                    <th class="text-center">{{ __('frontend.str.sent') }}</th>
                                    <th class="text-center">{{ __('frontend.str.unsent') }}</th>
                                    <th class="text-center">{{ __('frontend.str.read') }}</th>
                                    <th class="text-end">{{ __('frontend.str.excel_report') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list me-1"></i>
                            {{ __('frontend.str.newsletter') }}
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="logList" class="table table-striped table-hover mb-0 align-middle nowrap">
                                <thead>
                                <tr>
                                    <th class="log-id-cell text-center">ID</th>
                                    <th>{{ __('frontend.str.newsletter') }}</th>
                                    <th>E-mail</th>
                                    <th class="log-time-cell">{{ __('frontend.str.time') }}</th>
                                    <th class="text-center">{{ __('frontend.str.status') }}</th>
                                    <th class="text-center">{{ __('frontend.str.read') }}</th>
                                    <th>{{ __('frontend.str.error') }}</th>
                                </tr>
                                </thead>
                                <tbody>
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

    <!-- DataTables  & Plugins -->
    <script src="{{ asset('vendor/datatables/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('vendor/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script>
        let itemListTable;
        let logListTable;

        function toggleClearLogLoading(isLoading) {
            const clearButton = $('#clearLogButton');

            clearButton.toggleClass('disabled', isLoading);
            clearButton.attr('aria-disabled', isLoading ? 'true' : 'false');
            clearButton.css('pointer-events', isLoading ? 'none' : '');
            $('#clearLogSpinner').toggleClass('d-none', !isLoading);
        }

        $(function () {
            itemListTable = $('#itemList').DataTable({
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
                        "sPrevious": "{{ __('pagination.s_paginate.previous') }}",
                    },
                },
                "sDom": "lrtip",
                "autoWidth": false,
                "responsive": true,
                'createdRow': function (row, data, dataIndex) {
                    $(row).attr('id', 'rowid_' + data['id']);
                },
                aaSorting: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.datatable.logs') }}'
                },
                columns: [
                    {data: 'id', name: 'schedule.id'},
                    {data: 'event_start', name: 'event_start'},
                    {data: 'count', name: 'count', searchable: false},
                    {data: 'sent', name: 'sent', searchable: false},
                    {data: 'unsent', name: 'unsent', searchable: false},
                    {data: 'read_mail', name: 'read_mail', searchable: false},
                    {data: 'report', name: 'report', orderable: false, searchable: false},
                ],
                columnDefs: [
                    {targets: 0, className: 'log-id-cell text-center', width: '72px'},
                    {targets: 1, className: 'log-time-cell', width: '170px'},
                    {targets: [2, 3, 4, 5], className: 'text-center'},
                    {targets: 6, className: 'text-end'}
                ],
            });

            logListTable = $('#logList').DataTable({
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
                        "sPrevious": "{{ __('pagination.s_paginate.previous') }}",
                    },
                    "sSearch": ' <i class="fas fa-search" aria-hidden="true"></i>'
                },
                "sDom": "flrtip",
                "autoWidth": false,
                "responsive": true,
                'createdRow': function (row, data, dataIndex) {
                    $(row).attr('id', 'rowid_' + data['id']);
                    if (data['status'] === 0) $(row).attr('class', 'table-danger');
                },
                aaSorting: [[3, 'desc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.datatable.info_log') }}'
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'template', name: 'template'},
                    {data: 'email', name: 'email'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'success', name: 'success', searchable: false},
                    {data: 'readMail', name: 'readMail', searchable: false},
                    {data: 'errorMsg', name: 'errorMsg', orderable: false, searchable: false},
                ],
                columnDefs: [
                    {targets: 0, className: 'log-id-cell text-center', width: '72px'},
                    {targets: 3, className: 'log-time-cell', width: '170px'},
                    {targets: [4, 5], className: 'text-center'},
                    {targets: 6, className: 'log-error-cell'}
                ],
            });
        })

        function confirmation(event) {
            if ($('#clearLogButton').hasClass('disabled')) {
                event.preventDefault();
                return;
            }

            Swal.fire({
                title: "{{ __('frontend.str.clear_confirmation') }}",
                text: "{{ __('frontend.str.want_to_log_clear') }}",
                showCancelButton: true,
                icon: 'warning',
                cancelButtonText: "{{ __('frontend.str.cancel') }}",
                confirmButtonText: "{{ __('frontend.str.yes') }}",
                reverseButtons: true,
                confirmButtonColor: "#DD6B55",
                customClass: {
                    actions: 'my-actions',
                    cancelButton: 'order-1',
                },
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                toggleClearLogLoading(true);

                $.ajax({
                    url: "{{ route('admin.log.clear') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message || "{{ __('frontend.msg.data_successfully_deleted') }}",
                            confirmButtonText: 'OK'
                        });

                        if (itemListTable) {
                            itemListTable.ajax.reload(null, false);
                        }

                        if (logListTable) {
                            logListTable.ajax.reload(null, false);
                        }
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON || {};

                        Swal.fire({
                            icon: 'error',
                            title: response.message || "{{ __('frontend.str.delete_error') }}",
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function () {
                        toggleClearLogLoading(false);
                    }
                });
            })
        }

    </script>

@endsection
