@extends('admin.app')

@section('title', $title)

@section('css')

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">

    <style>
        .log-info-page #itemList {
            width: 100% !important;
        }

        .log-info-page #itemList th,
        .log-info-page #itemList td {
            vertical-align: middle;
        }

        .log-info-page #itemList thead th {
            white-space: nowrap;
        }

        .log-info-page .log-error-cell {
            max-width: 360px;
            white-space: normal;
        }

        .log-info-page .log-id-cell {
            max-width: 72px;
            min-width: 64px;
            white-space: nowrap;
            width: 72px !important;
        }
    </style>

@endsection

@section('content')

    <div class="container-fluid log-info-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list me-1"></i>
                            {{ $title }}
                        </h3>

                        <div class="card-tools">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.log.index') }}">
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
                                    <th class="log-id-cell text-center">ID</th>
                                    <th>{{ __('frontend.str.newsletter') }}</th>
                                    <th>E-mail</th>
                                    <th>{{ __('frontend.str.time') }}</th>
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
                aaSorting: [[3, 'desc']],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.datatable.info_log', ['id' => $id]) }}'
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
                    {targets: [4, 5], className: 'text-center'},
                    {targets: 6, className: 'log-error-cell'}
                ],
            });
        })

    </script>
@endsection
