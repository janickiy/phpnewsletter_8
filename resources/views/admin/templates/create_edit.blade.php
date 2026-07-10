@extends('admin.app')

@section('title', $title)

@section('css')

    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs5.min.css') }}">
    <!-- CodeMirror -->
    <link rel="stylesheet" href="{{ asset('plugins/codemirror/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/codemirror/theme/monokai.css') }}">

    <style>
        .template-editor-page .note-editor {
            margin-bottom: 0;
        }

        .template-editor-page .template-help {
            background-color: var(--bs-tertiary-bg);
            border-left: 3px solid var(--bs-primary);
            border-radius: var(--bs-border-radius);
            color: var(--bs-secondary-color);
            margin-top: 1rem;
            padding: .75rem 1rem;
        }

        .template-editor-page .template-side-section {
            border: 1px solid var(--bs-border-color);
            border-radius: var(--bs-border-radius);
            padding: 1rem;
        }

        .template-editor-page .template-side-section + .template-side-section {
            margin-top: 1rem;
        }

        .template-editor-page .template-section-title {
            align-items: center;
            display: flex;
            font-size: 1rem;
            font-weight: 600;
            gap: .45rem;
            margin-bottom: 1rem;
        }

        .template-editor-page .attachment-list {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .template-editor-page .attachment-item {
            align-items: center;
            background-color: var(--bs-tertiary-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: var(--bs-border-radius);
            display: flex;
            gap: .5rem;
            justify-content: space-between;
            padding: .5rem .75rem;
        }
    </style>

@endsection

@section('content')

    @php
        $selectedPrior = old('prior', $template->prior ?? 3);
    @endphp

    {!! form_open(['url' => isset($template) ? route('admin.templates.update') : route('admin.templates.store'), 'files' => true, 'method' => isset($template) ? 'put' : 'post', 'id' => 'tmplForm']) !!}

    {!! isset($template) ? form_hidden('id', $template->id) : '' !!}

    <div class="container-fluid template-editor-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-envelope-open-text me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row">
                            <div class="col-xl-8">
                                <div class="form-group mb-3">
                                    {!! form_label('name', __('frontend.form.name') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('name', old('name', $template->name ?? null), ['class' => 'form-control', 'placeholder' => __('frontend.form.name')]) !!}

                                    @if ($errors->has('name'))
                                        <p class="text-danger mb-0">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>

                                <div class="form-group mb-3">
                                    {!! form_label('body', __('frontend.form.template') . '*', ['class' => 'form-label']) !!}
                                    {!! form_textarea('body', old('body', $template->body ?? null), ['rows' => '8', 'placeholder' => __('frontend.form.template'), 'class' => 'form-control']) !!}

                                    @if ($errors->has('body'))
                                        <p class="text-danger mb-0">{{ $errors->first('body') }}</p>
                                    @endif

                                    <div class="template-help">
                                        <small>{!! __('frontend.note.personalization') !!}</small>
                                    </div>

                                    @if($macrosList)
                                        <div class="template-help">
                                            <small>{!! __('frontend.note.macros') !!} {!! $macrosList !!}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="template-side-section">
                                    <div class="template-section-title">
                                        <i class="fas fa-sliders-h"></i>
                                        {{ __('frontend.form.prior') }}
                                    </div>

                                    <div class="form-check mb-2">
                                        {!! form_radio('prior', 3, (string) $selectedPrior === '3', ['class' => 'form-check-input', 'id' => 'prior_normal']) !!}
                                        {!! form_label('prior_normal', __('frontend.form.normal'), ['class' => 'form-check-label']) !!}
                                    </div>

                                    <div class="form-check mb-2">
                                        {!! form_radio('prior', 2, (string) $selectedPrior === '2', ['class' => 'form-check-input', 'id' => 'prior_low']) !!}
                                        {!! form_label('prior_low', __('frontend.form.low'), ['class' => 'form-check-label']) !!}
                                    </div>

                                    <div class="form-check">
                                        {!! form_radio('prior', 1, (string) $selectedPrior === '1', ['class' => 'form-check-input', 'id' => 'prior_high']) !!}
                                        {!! form_label('prior_high', __('frontend.form.high'), ['class' => 'form-check-label']) !!}
                                    </div>

                                    @if ($errors->has('prior'))
                                        <p class="text-danger mb-0 mt-2">{{ $errors->first('prior') }}</p>
                                    @endif
                                </div>

                                <div class="template-side-section">
                                    <div class="template-section-title">
                                        <i class="fas fa-paperclip"></i>
                                        {{ __('frontend.str.attachments') }}
                                    </div>

                                    <div class="form-group mb-3">
                                        {!! form_label('attachfile[]', __('frontend.form.attach_files'), ['class' => 'form-label']) !!}
                                        {!! form_file('attachfile[]', ['id' => 'attachfile', 'multiple' => true, 'class' => 'form-control']) !!}

                                        @if ($errors->has('attachfile'))
                                            <p class="text-danger mb-0">{{ $errors->first('attachfile') }}</p>
                                        @endif
                                    </div>

                                    <div class="attachment-list">
                                        @forelse($attachment ?? [] as $a)
                                            <div id="attach_{{ $a->id }}" class="attachment-item">
                                                <span class="text-truncate">{{ $a->file_name }}</span>
                                                <a href="#" data-num="{{ $a->id }}" class="btn btn-outline-danger btn-sm remove_attach" title="{{ __('frontend.str.remove') }}">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        @empty
                                            <span class="text-muted">{{ __('frontend.str.no') }}</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="template-side-section">
                                    <div class="template-section-title">
                                        <i class="fas fa-paper-plane"></i>
                                        {{ __('frontend.str.send_test_letter') }}
                                        <span id="process"></span>
                                    </div>

                                    <div id="resultSend"></div>

                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        {!! form_text('email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'id' => 'email']) !!}
                                        <button type="button" id="send_test" class="btn btn-info">
                                            {{ __('frontend.str.send') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex flex-column flex-sm-row gap-2 justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($template) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary" href="{{ route('admin.templates.index') }}">
                            {{ __('frontend.form.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! form_close() !!}

@endsection

@section('js')

    <!-- Summernote -->
    <script src="{{ asset('plugins/summernote/summernote-bs5.min.js') }}"></script>

    <!-- CodeMirror -->
    <script src="{{ asset('plugins/codemirror/codemirror.js') }}"></script>
    <script src="{{ asset('plugins/codemirror/mode/css/css.js') }}"></script>
    <script src="{{ asset('plugins/codemirror/mode/xml/xml.js') }}"></script>
    <script src="{{ asset('plugins/codemirror/mode/htmlmixed/htmlmixed.js') }}"></script>
    <!-- Page specific script -->
    <script>
        $(function () {
            // Summernote
            $('#body').summernote();

            $(document).on("click", ".remove_attach", function () {
                let idAttach = $(this).attr('data-num');

                let request = $.ajax({
                    url: '{{ route('admin.ajax.action') }}',
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        action: "remove_attach",
                        id: idAttach,
                    },

                    dataType: "json"
                });

                request.done(function (data) {
                    if (data.result != null && data.result === true) {
                        $("#attach_" + idAttach).remove();
                    }
                });
            });

            $(document).on("click", "#send_test", function () {
                let bodyContent = $('#body').val();
                let arr = $("#tmplForm").serializeArray();
                let aParams = [];
                let sParam;

                $("#process").removeClass().addClass('showprocess');
                $("#send_test").attr('disabled', 'disabled');

                for (let i = 0, count = arr.length; i < count; i++) {
                    sParam = encodeURIComponent(arr[i].name);

                    if (sParam == 'body') {
                        sParam += "=";
                        sParam += encodeURIComponent(bodyContent);
                    } else {
                        sParam += "=";
                        sParam += encodeURIComponent(arr[i].value);
                    }

                    aParams.push(sParam);
                }

                sParam = 'action';
                sParam += "=";
                sParam += encodeURIComponent('send_test_email');
                aParams.push(sParam);

                let sendData = aParams.join("&");
                let request = $.ajax({
                    url: '{{ route('admin.ajax.action') }}',
                    method: "POST",
                    data: sendData,
                    dataType: "json"
                });

                request.done(function (data) {
                    if (data.result != null) {
                        let alert_msg = '';

                        if (data.result === true) {
                            alert_msg += '<div class="alert alert-success alert-dismissible fade show">';
                            alert_msg += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            alert_msg += data.msg;
                            alert_msg += '</div>';
                        } else {
                            alert_msg += '<div class="alert alert-danger alert-dismissible fade show">';
                            alert_msg += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            alert_msg += data.msg;
                            alert_msg += '</div>';
                        }

                        console.log(data.msg);

                        $("#resultSend").html(alert_msg);
                        $("#process").removeClass();
                        $("#send_test").removeAttr('disabled');
                    }
                });
            });
        })

    </script>

@endsection
