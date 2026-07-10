@extends('admin.app')

@section('title', $title)

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
    </style>

@endsection

@section('content')

    <div class="container-fluid redirect-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-mouse-pointer me-1"></i>
                            {{ __('frontend.menu.redirect') }}
                        </h3>

                        @if(PermissionsHelper::has_permission('admin'))
                            <div class="card-tools d-flex align-items-center gap-2">
                                <a id="clearRedirectButton"
                                   class="btn btn-outline-danger btn-sm"
                                   title="{{ __('frontend.str.log_clear') }}"
                                   onclick="confirmation(event)">
                                    <i class="fas fa-trash me-1"></i>
                                    {{ __('frontend.str.redirect_clear') }}
                                </a>

                                <span id="clearRedirectSpinner" class="d-none">
                                    <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="itemList" class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th>URL</th>
                                    <th>{{ __('frontend.str.redirect_number') }}</th>
                                    <th>{{ __('frontend.str.excel_report') }}</th>
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
            });
        });

        function confirmation(event) {
            if ($('#clearRedirectButton').hasClass('disabled')) {
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
