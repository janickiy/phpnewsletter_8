@extends('admin.app')

@section('title', $title)

@section('css')

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">

    <style>
        .subscribers-page #itemList {
            width: 100% !important;
        }

        .subscribers-page #itemList th,
        .subscribers-page #itemList td {
            vertical-align: middle;
        }

        .subscribers-page #itemList thead th {
            white-space: nowrap;
        }

        .subscribers-tools {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            justify-content: flex-end;
        }

        .subscribers-bulk-actions {
            max-width: 280px;
        }
    </style>

@endsection

@section('content')

    <div class="container-fluid subscribers-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-friends me-1"></i>
                            {{ __('frontend.menu.subscribers') }}
                        </h3>

                        <div class="card-tools subscribers-tools">
                            <a class="btn btn-outline-secondary btn-sm"
                               title="{{ __('frontend.str.import_subscribers') }}"
                               href="{{ route('admin.subscribers.import') }}">
                                <i class="fas fa-download me-1"></i>
                                {{ __('frontend.str.import') }}
                            </a>

                            <a class="btn btn-outline-secondary btn-sm"
                               title="{{ __('frontend.str.export_subscribers') }}"
                               href="{{ route('admin.subscribers.export') }}">
                                <i class="fas fa-upload me-1"></i>
                                {{ __('frontend.str.export') }}
                            </a>

                            @if($canRemoveAllSubscribers)
                                <a id="removeAllSubscribersButton"
                                   class="btn btn-outline-danger btn-sm"
                                   title="{{ __('frontend.str.delete_all_subscribers') }}"
                                   onclick="confirmation(event)">
                                    <i class="fas fa-trash me-1"></i>
                                    {{ __('frontend.str.delete_all') }}
                                </a>

                                <span id="removeAllSubscribersSpinner" class="align-self-center d-none">
                                    <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                                </span>
                            @endif

                            <a href="{{ route('admin.subscribers.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('frontend.str.add_subscriber') }}
                            </a>
                        </div>
                    </div>

                    {!! form_open(['url' => route('admin.subscribers.status'), 'method' => 'post']) !!}

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="itemList" class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 48px">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               title="{{ __('frontend.str.check_uncheck_all') }}"
                                               id="checkAll">
                                    </th>
                                    <th>{{ __('frontend.str.name') }}</th>
                                    <th>E-mail</th>
                                    <th>{{ __('frontend.str.category') }}</th>
                                    <th>{{ __('frontend.str.projects') }}</th>
                                    <th>{{ __('frontend.str.status') }}</th>
                                    <th>{{ __('frontend.str.added') }}</th>
                                    <th class="text-end" style="width: 10%">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-body-tertiary">
                        <div class="input-group input-group-sm subscribers-bulk-actions">
                            <span class="input-group-text">
                                <i class="fas fa-tasks"></i>
                            </span>

                            {!! form_select('action',[
                                '1' => __('frontend.str.activate'),
                                '0' => __('frontend.str.deactivate'),
                                '2' => __('frontend.str.remove')
                            ],null,['class' => 'form-select', 'id' => 'select_action','placeholder' => '--' . __('frontend.str.action') . '--']) !!}

                            {!! form_submit(__('frontend.str.apply'), ['class' => 'btn btn-success', 'disabled' => "", 'id' => 'apply']) !!}
                        </div>
                    </div>

                    {!! form_close() !!}
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

        $(function () {
            $("#apply").click(function (event) {
                let idSelect = $('#select_action').val();

                if (idSelect === '') {
                    event.preventDefault();
                    Swal.fire({
                        title: "Error",
                        text: "{{ __('frontend.str.select_action') }}",
                        icon: "error",
                        showCancelButton: false,
                        cancelButtonText: "{{ __('frontend.str.cancel') }}",
                        confirmButtonColor: "#DD6B55",
                        closeOnConfirm: false
                    });
                } else {
                    if (idSelect === '2') {
                        event.preventDefault();
                        let form = $(this).parents('form');
                        Swal.fire({
                            title: "{{ __('frontend.str.delete_confirmation') }}",
                            text: "{{ __('frontend.str.confirm_remove') }}",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "{{ __('frontend.str.yes') }}",
                            cancelButtonText: "{{ __('frontend.str.cancel') }}",
                            closeOnConfirm: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                }
            });

            $("#checkAll").on('click change', function () {
                $('#itemList').find('input.check').prop('checked', this.checked);
                countChecked();
            });

            $("#itemList").on('change', 'input.check', function () {
                syncCheckAllState();
                countChecked();
            });

            $("#itemList").DataTable({
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
                    if (data['activeStatus'] === 0) $(row).addClass('table-secondary');
                },
                aaSorting: [[6, 'desc']],
                "processing": true,
                "responsive": true,
                "autoWidth": false,
                "deferRender": true,
                "searchDelay": 500,
                'serverSide': true,
                'ajax': {
                    url: '{{ route('admin.datatable.subscribers') }}'
                },
                columnDefs: [
                    {targets: 0, className: 'text-center', width: '48px'},
                    {targets: [5, 6, 7], className: 'text-nowrap'},
                    {targets: 7, className: 'text-end'}
                ],
                'columns': [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'subscriptions', name: 'subscriptions', orderable: false, searchable: false},
                    {data: 'projects', name: 'projects', orderable: false, searchable: false},
                    {data: 'active', name: 'active', searchable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                drawCallback: function () {
                    syncCheckAllState();
                    countChecked();
                }
            });

            $('#itemList').on('click', 'a.deleteRow', function () {
                let rowid = $(this).attr('id');
                Swal.fire({
                    title: "{{ __('frontend.msg.are_you_sure') }}",
                    text: "{{ __('frontend.msg.will_not_be_able_to_recover_information') }}",
                    showCancelButton: true,
                    icon: 'warning',
                    cancelButtonText: "{{ __('frontend.str.cancel') }}",
                    confirmButtonText: "{{ __('frontend.msg.yes_remove') }}",
                    reverseButtons: true,
                    confirmButtonColor: "#DD6B55",
                    customClass: {
                        actions: 'my-actions',
                        cancelButton: 'order-1',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('subscribers/destroy') }}/' + rowid,
                            type: "POST",
                            dataType: "html",
                            data: {_method: 'DELETE'},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function () {
                                $("#rowid_" + rowid).remove();
                                syncCheckAllState();
                                countChecked();
                                Swal.fire("{{ __('frontend.msg.done') }}", "{{ __('frontend.msg.data_successfully_deleted') }}", 'success');
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                Swal.fire("{{ __('frontend.msg.error_deleting') }}", "{{ __('frontend.msg.try_again') }}", 'error');
                                console.log(ajaxOptions);
                                console.log(thrownError);
                            }
                        });
                    }
                });
            });
        });

        function countChecked() {
            $('#apply').prop('disabled', $('#itemList').find('input.check:checked').length === 0);
        }

        function syncCheckAllState() {
            const total = $('#itemList').find('input.check').length;
            const checked = $('#itemList').find('input.check:checked').length;
            $('#checkAll').prop('checked', total > 0 && total === checked);
        }

        @if($canRemoveAllSubscribers)
            function toggleRemoveAllSubscribersLoading(isLoading) {
                const removeButton = $('#removeAllSubscribersButton');

                removeButton.toggleClass('disabled', isLoading);
                removeButton.attr('aria-disabled', isLoading ? 'true' : 'false');
                removeButton.css('pointer-events', isLoading ? 'none' : '');
                $('#removeAllSubscribersSpinner').toggleClass('d-none', !isLoading);
            }

            $(window).on('pageshow', function () {
                toggleRemoveAllSubscribersLoading(false);
            });

            function confirmation(event) {
                if ($('#removeAllSubscribersButton').hasClass('disabled')) {
                    event.preventDefault();
                    return;
                }

                Swal.fire({
                    title: "{{ __('frontend.str.delete_all_subscribers') }}",
                    text: "{{ __('frontend.str.want_to_delete_all_subscribers')  }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{ __('frontend.str.yes') }}",
                    cancelButtonText: "{{ __('frontend.str.cancel') }}",
                    closeOnConfirm: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        toggleRemoveAllSubscribersLoading(true);
                        window.location.href = "{{ route('admin.subscribers.remove_all') }}";
                    }
                });
            }
        @endif

    </script>

@endsection
