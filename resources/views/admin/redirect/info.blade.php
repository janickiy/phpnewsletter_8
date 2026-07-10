@extends('admin.app')

@section('title', $title)

@section('css')

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">

    <style>
        .redirect-info-page #itemList {
            width: 100% !important;
        }

        .redirect-info-page #itemList th,
        .redirect-info-page #itemList td {
            vertical-align: middle;
        }

        .redirect-info-page #itemList thead th {
            white-space: nowrap;
        }

        .redirect-info-page .redirect-id-cell {
            max-width: 72px;
            min-width: 64px;
            white-space: nowrap;
            width: 72px !important;
        }

        .redirect-info-page .redirect-time-cell {
            max-width: 170px;
            min-width: 150px;
            white-space: nowrap;
            width: 170px !important;
        }

        .redirect-info-page .card-body.p-0 .table-responsive {
            padding: 1rem;
        }

        .redirect-info-page .card-body.p-0 .table-responsive > .dt-container {
            width: 100%;
        }

        .redirect-info-page .card-body.p-0 .table-responsive > .dt-container > .row {
            margin-left: 0;
            margin-right: 0;
        }

        .redirect-info-page .card-body.p-0 .table-responsive > .dt-container > .row:first-child {
            align-items: center;
            border-bottom: 1px solid var(--bs-border-color);
            padding-bottom: .75rem;
            padding-top: 0;
        }

        .redirect-info-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child {
            align-items: center;
            border-top: 1px solid var(--bs-border-color);
            justify-content: center;
            margin-bottom: 0;
            margin-top: 0 !important;
            padding-bottom: 0;
            padding-top: 1rem;
            row-gap: .75rem;
        }

        .redirect-info-page .card-body.p-0 table.dataTable {
            margin-bottom: .75rem !important;
            margin-top: .75rem !important;
        }

        .redirect-info-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child > .dt-layout-start {
            flex: 0 0 100%;
            max-width: 100%;
            text-align: left;
        }

        .redirect-info-page .card-body.p-0 .table-responsive > .dt-container > .row:last-child > .dt-layout-end {
            display: flex;
            flex: 0 0 100%;
            justify-content: center;
            margin-left: 0 !important;
            max-width: 100%;
        }

        .redirect-info-page .dt-length,
        .redirect-info-page .dt-search,
        .redirect-info-page .dt-info,
        .redirect-info-page .dt-paging {
            padding: 0;
        }

        .redirect-info-page .dt-search {
            align-items: center;
            display: flex;
            gap: .5rem;
            justify-content: flex-end;
        }

        .redirect-info-page .dt-search label {
            margin-bottom: 0;
        }

        .redirect-info-page .dt-search input {
            margin-left: 0;
        }

        .redirect-info-page .dt-paging .pagination {
            justify-content: center;
            margin-bottom: 0;
        }
    </style>

@endsection

@section('content')

    <div class="container-fluid redirect-info-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line me-1"></i>
                            {{ $title }}
                        </h3>

                        <div class="card-tools">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.redirect.index') }}">
                                <i class="fas fa-arrow-left me-1"></i>
                                {{ __('frontend.str.back') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="itemList" class="table table-striped table-hover mb-0 align-middle nowrap">
                                <thead>
                                <tr>
                                    <th class="redirect-id-cell text-center">ID</th>
                                    <th>Email</th>
                                    <th class="redirect-time-cell">{{ __('frontend.str.time') }}</th>
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

        $(document).ready(function () {
            $('#itemList').dataTable({
                "sDom": "flrtip",
                "autoWidth": false,
                "responsive": true,
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
                'createdRow': function (row, data, dataIndex) {
                    $(row).attr('id', 'rowid_' + data['id']);
                    if (data['status'] === 0) $(row).attr('class', 'table-danger');
                },
                aaSorting: [[1, 'asc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.datatable.info_redirect', ['url' => $url]) }}'
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'email', name: 'email'},
                    {data: 'created_at', name: 'created_at'},
                ],
                columnDefs: [
                    {targets: 0, className: 'redirect-id-cell text-center', width: '72px'},
                    {targets: 2, className: 'redirect-time-cell', width: '170px'}
                ],
            });
        })

    </script>
@endsection
