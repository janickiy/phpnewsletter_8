@extends('admin.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    @php
        $selectedSecure = old('secure', $row->secure ?? 'no');
        $selectedAuthentication = old('authentication', $row->authentication ?? 'no');
        $selectedAuthentication = $selectedAuthentication === 'crammd5' ? 'cram-md5' : $selectedAuthentication;
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas {{ isset($row) ? 'fa-pen' : 'fa-plus' }} me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    {!! form_open(['url' => isset($row) ? route('admin.smtp.update') : route('admin.smtp.store'), 'method' => isset($row) ? 'put' : 'post']) !!}

                    {!! isset($row) ? form_hidden('id', $row->id) : '' !!}

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('host', __('frontend.form.smtp_server') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('host', old('host', $row->host ?? null), ['placeholder' => __('frontend.form.smtp_server'), 'class' => 'form-control' . ($errors->has('host') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('host'))
                                        <div class="invalid-feedback">{{ $errors->first('host') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('email', 'E-mail*', ['class' => 'form-label']) !!}
                                    {!! form_text('email', old('email', $row->email ?? null), ['placeholder' => 'mail@example.com', 'class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'autocomplete' => 'email']) !!}

                                    @if ($errors->has('email'))
                                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('username', __('frontend.form.login') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('username', old('username', $row->username ?? null), ['placeholder' => __('frontend.form.login'), 'class' => 'form-control' . ($errors->has('username') ? ' is-invalid' : ''), 'autocomplete' => 'username']) !!}

                                    @if ($errors->has('username'))
                                        <div class="invalid-feedback">{{ $errors->first('username') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('password', __('frontend.form.password') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('password', old('password', $row->password ?? null), ['placeholder' => __('frontend.form.password'), 'class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'autocomplete' => 'new-password']) !!}

                                    @if ($errors->has('password'))
                                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    {!! form_label('port', __('frontend.form.port') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('port', old('port', $row->port ?? 25), ['placeholder' => __('frontend.form.port'), 'class' => 'form-control' . ($errors->has('port') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('port'))
                                        <div class="invalid-feedback">{{ $errors->first('port') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    {!! form_label('timeout', __('frontend.form.timeout') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('timeout', old('timeout', $row->timeout ?? 5), ['placeholder' => __('frontend.form.timeout'), 'class' => 'form-control' . ($errors->has('timeout') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('timeout'))
                                        <div class="invalid-feedback">{{ $errors->first('timeout') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-body-tertiary h-100">
                                    <div class="fw-semibold mb-2">{{ __('frontend.form.secure_connection') }}</div>

                                    <div class="form-check mb-2">
                                        {!! form_radio('secure', 'no', $selectedSecure === 'no', ['class' => 'form-check-input', 'id' => 'secure_no']) !!}
                                        {!! form_label('secure_no', __('frontend.str.no'), ['class' => 'form-check-label']) !!}
                                    </div>

                                    <div class="form-check mb-2">
                                        {!! form_radio('secure', 'ssl', $selectedSecure === 'ssl', ['class' => 'form-check-input', 'id' => 'secure_ssl']) !!}
                                        {!! form_label('secure_ssl', 'ssl', ['class' => 'form-check-label']) !!}
                                    </div>

                                    <div class="form-check">
                                        {!! form_radio('secure', 'tls', $selectedSecure === 'tls', ['class' => 'form-check-input', 'id' => 'secure_tls']) !!}
                                        {!! form_label('secure_tls', 'tls', ['class' => 'form-check-label']) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="border rounded p-3 bg-body-tertiary h-100">
                                    <div class="fw-semibold mb-2">{{ __('frontend.form.authentication_method') }}</div>

                                    <div class="form-check mb-2">
                                        {!! form_radio('authentication', 'no', $selectedAuthentication === 'no', ['class' => 'form-check-input', 'id' => 'authentication_no']) !!}
                                        {!! form_label('authentication_no', 'LOGIN (' . __('frontend.form.low_secrecy') . ')', ['class' => 'form-check-label']) !!}
                                    </div>

                                    <div class="form-check mb-2">
                                        {!! form_radio('authentication', 'plain', $selectedAuthentication === 'plain', ['class' => 'form-check-input', 'id' => 'authentication_plain']) !!}
                                        {!! form_label('authentication_plain', 'PLAIN (' . __('frontend.form.medium_secrecy') . ')', ['class' => 'form-check-label']) !!}
                                    </div>

                                    <div class="form-check">
                                        {!! form_radio('authentication', 'cram-md5', $selectedAuthentication === 'cram-md5', ['class' => 'form-check-input', 'id' => 'authentication_cram_md5']) !!}
                                        {!! form_label('authentication_cram_md5', 'CRAM-MD5 (' . __('frontend.form.high_secrecy') . ')', ['class' => 'form-check-label']) !!}
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('connection'))
                                <div class="col-12">
                                    <div class="alert alert-danger mb-0">{{ $errors->first('connection') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer d-flex flex-column flex-sm-row gap-2 justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary" href="{{ route('admin.smtp.index') }}">
                            {{ __('frontend.form.back') }}
                        </a>
                    </div>

                    {!! form_close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

@endsection
