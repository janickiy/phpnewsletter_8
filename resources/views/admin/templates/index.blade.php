@extends('admin.app')

@section('title', $title)

@section('css')

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">

    <style>
        .templates-page #itemList {
            width: 100% !important;
        }

        .templates-page #itemList th,
        .templates-page #itemList td {
            vertical-align: middle;
        }

        .templates-page #itemList thead th {
            white-space: nowrap;
        }

        .template-cell-title {
            font-weight: 600;
        }

        .template-cell-excerpt {
            display: block;
            line-height: 1.35;
            margin-top: .35rem;
        }

        .templates-bulk-actions {
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

    <div class="container-fluid templates-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-envelope-open-text me-1"></i>
                            {{ __('frontend.menu.templates') }}
                        </h3>

                        <div class="card-tools">
                            <a href="{{ route('admin.templates.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('frontend.str.add_template') }}
                            </a>
                        </div>
                    </div>

                    {!! form_open(['url' => route('admin.templates.status'), 'method' => 'post']) !!}

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
                                    <th style="width: 72px">ID</th>
                                    <th>{{ __('frontend.str.template') }}</th>
                                    <th>{{ __('frontend.str.importance') }}</th>
                                    <th>{{ __('frontend.str.attachments') }}</th>
                                    <th>{{ __('frontend.str.date') }}</th>
                                    <th class="text-end" style="width: 10%">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-body-tertiary">
                        <div class="input-group input-group-sm templates-bulk-actions">
                            <span class="input-group-text">
                                <i class="fas fa-list-check"></i>
                            </span>

                            {!! form_select('action',[
                                '0' => __('frontend.str.send'),
                                '1' => __('frontend.str.remove')
                            ],null,['class' => 'form-select', 'id' => 'select_action','placeholder' => '--' . __('frontend.str.action') . '--'],[0 => ['data-id' => 'sendmail', 'class' => 'open_modal']]) !!}

                            {!! form_submit(__('frontend.str.apply'), ['class' => 'btn btn-success', 'disabled' => "", 'id' => 'apply']) !!}
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
                        {!! form_select('categoryId[]', $categoryOptions, null, ['id' => 'categoryId','multiple'=>'multiple', 'placeholder' => __('frontend.form.select_category'), 'class' => 'form-select custom-scroll', 'style' => 'width: 100%']) !!}
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
            const openModalButton = $('#apply');
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

            openModalButton.on('click', function (event) {
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
                $('#itemList').find('input.check').prop('checked', this.checked);
                countChecked();
            });

            $('#itemList').on('change', 'input.check', function () {
                syncCheckAllState();
                countChecked();
            });

            $('#itemList').DataTable({
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
                createdRow: function (row, data) {
                    $(row).attr('id', 'rowid_' + data.id);
                },
                aaSorting: [[1, 'asc']],
                processing: true,
                responsive: true,
                autoWidth: false,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.datatable.templates') }}'
                },
                columnDefs: [
                    {targets: 0, className: 'text-center', width: '48px'},
                    {targets: 1, width: '72px'},
                    {targets: [3, 4], className: 'text-nowrap'},
                    {targets: 6, className: 'text-end text-nowrap'}
                ],
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'prior', name: 'prior', searchable: false},
                    {data: 'attach', name: 'attach.id', searchable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                drawCallback: function () {
                    syncCheckAllState();
                    countChecked();
                }
            });

            $('#itemList').on('click', 'a.deleteRow', function () {
                const rowid = $(this).attr('id');
                Swal.fire({
                    title: "{{ __('frontend.msg.are_you_sure') }}",
                    text: "{{ __('frontend.msg.will_not_be_able_to_recover_information') }}",
                    showCancelButton: true,
                    icon: 'warning',
                    cancelButtonText: "{{ __('frontend.str.cancel') }}",
                    confirmButtonText: "{{ __('frontend.msg.yes_remove') }}",
                    reverseButtons: true,
                    confirmButtonColor: '#DD6B55',
                    customClass: {
                        actions: 'my-actions',
                        cancelButton: 'order-1',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('template/destroy') }}/' + rowid,
                            type: 'POST',
                            dataType: 'html',
                            data: {_method: 'DELETE'},
                            headers: {'X-CSRF-TOKEN': csrfToken},
                            success: function () {
                                $('#rowid_' + rowid).remove();
                                Swal.fire("{{ __('frontend.msg.done') }}", "{{ __('frontend.msg.data_successfully_deleted') }}", 'success');
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                Swal.fire("{{ __('frontend.msg.error_deleting') }}", "{{ __('frontend.msg.try_again') }}", 'error');
                                console.log(xhr);
                                console.log(ajaxOptions);
                                console.log(thrownError);
                            }
                        });
                    }
                });
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
            return $('#itemList').find('input.check:checked').map(function () {
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
            const total = $('#itemList').find('input.check').length;
            const checked = $('#itemList').find('input.check:checked').length;
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
