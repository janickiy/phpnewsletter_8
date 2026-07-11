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
        .redirect-page #itemList {
            width: 100% !important;
        }

        .redirect-page #itemList th,
        .redirect-page #itemList td {
            vertical-align: middle;
        }

        .redirect-page #itemList thead th {
            white-space: nowrap;
        }

        .redirect-page .redirect-clear-action {
            min-width: 0;
        }

        .redirect-page .redirect-url-cell {
            min-width: 320px;
            white-space: normal;
            word-break: break-word;
        }

        .redirect-page .card-body.p-0 .table-responsive {
            padding: 1rem;
        }

        .redirect-page .card-body.p-0 .table-responsive > .dt-container {
            width: 100%;
        }

        .redirect-page .card-body.p-0 .table-responsive > .dt-container > .row {
            margin-left: 0;
            margin-right: 0;
        }

        .redirect-page .card-body.p-0 .table-responsive > .dt-container > .row:first-child {
            align-items: center;
            border-bottom: 1px solid var(--bs-border-color);
            padding-bottom: .75rem;
            padding-top: 0;
        }

        .redirect-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child {
            align-items: center;
            border-top: 1px solid var(--bs-border-color);
            justify-content: center;
            margin-bottom: 0;
            margin-top: 0 !important;
            padding-bottom: 0;
            padding-top: 1rem;
            row-gap: .75rem;
        }

        .redirect-page .card-body.p-0 table.dataTable {
            margin-bottom: .75rem !important;
            margin-top: .75rem !important;
        }

        .redirect-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child > .dt-layout-start {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: left;
        }

        .redirect-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child > .dt-layout-end {
            display: flex;
            flex: 0 0 100%;
            justify-content: center;
            margin-left: 0 !important;
            max-width: 100%;
        }

        .redirect-page .dt-length,
        .redirect-page .dt-search,
        .redirect-page .dt-info,
        .redirect-page .dt-paging {
            padding: 0;
        }

        .redirect-page .dt-search {
            align-items: center;
            display: flex;
            gap: .5rem;
            justify-content: flex-end;
        }

        .redirect-page .dt-search label {
            margin-bottom: 0;
        }

        .redirect-page .dt-search input {
            margin-left: 0;
        }

        .redirect-page .dt-paging .pagination {
            justify-content: center;
            margin-bottom: 0;
        }
    </style>

@endsection

@section('content')

    <div class="container-fluid redirect-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-link me-1"></i>
                            {{ __('frontend.title.redirect_index') }}
                        </h3>

                        @if(PermissionsHelper::has_permission('admin'))
                            <div class="card-tools d-flex align-items-center gap-2">
                                <button id="clearRedirectButton"
                                   type="button"
                                   class="btn btn-outline-danger btn-sm redirect-clear-action"
                                   title="{{ __('frontend.str.log_clear') }}"
                                   onclick="confirmation(event)">
                                    <i class="fas fa-trash me-1"></i>
                                    {{ __('frontend.str.redirect_clear') }}
                                </button>

                                <span id="clearRedirectSpinner" class="d-none">
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
                                    <th>URL</th>
                                    <th class="text-center">{{ __('frontend.str.redirect_number') }}</th>
                                    <th class="text-end">{{ __('frontend.str.excel_report') }}</th>
                                </tr>
                                </thead>
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
        let redirectTable;

        function toggleClearRedirectLoading(isLoading) {
            const clearButton = $('#clearRedirectButton');

            clearButton.toggleClass('disabled', isLoading);
            clearButton.attr('aria-disabled', isLoading ? 'true' : 'false');
            clearButton.css('pointer-events', isLoading ? 'none' : '');
            $('#clearRedirectSpinner').toggleClass('d-none', !isLoading);
        }

        $(function () {
            redirectTable = $('#itemList').DataTable({
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
                aaSorting: [[0, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.datatable.redirect') }}'
                },
                columns: [
                    {data: 'url', name: 'url'},
                    {data: 'count', name: 'count', searchable: false},
                    {data: 'report', name: 'report', orderable: false, searchable: false},
                ],
                columnDefs: [
                    {targets: 0, className: 'redirect-url-cell'},
                    {targets: 1, className: 'text-center'},
                    {targets: 2, className: 'text-end'}
                ],
            });
        });

        function confirmation(event) {
            if ($('#clearRedirectButton').hasClass('disabled')) {
                event.preventDefault();
                return;
            }

            Swal.fire({
                title: "{{ __('frontend.str.clear_confirmation') }}",
                text: "{{ __('frontend.str.want_to_redirect_clear') }}",
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

                toggleClearRedirectLoading(true);

                $.ajax({
                    url: "{{ route('admin.redirect.clear') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message || "{{ __('frontend.msg.data_successfully_deleted') }}",
                            confirmButtonText: 'OK'
                        });

                        if (redirectTable) {
                            redirectTable.ajax.reload(null, false);
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
                        toggleClearRedirectLoading(false);
                    }
                });
            })
        }

    </script>

@endsection
