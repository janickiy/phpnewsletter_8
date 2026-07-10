@extends('admin.app')

@section('title', $title)

@section('breadcrumbs')
    <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard.index') }}">{{ __('frontend.str.admin_panel') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.smtp.index') }}">{{ __('frontend.title.smtp_index') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ $title }}</li>
    </ol>
@endsection

@section('css')

@endsection

@section('content')

    @php
        $selectedSecure = old('secure', $row->secure ?? 'no');
        $selectedAuthentication = old('authentication', $row->authentication ?? 'login');
        $selectedAuthentication = $selectedAuthentication === 'no' ? 'login' : $selectedAuthentication;
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

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('port', __('frontend.form.port') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('port', old('port', $row->port ?? 25), ['placeholder' => __('frontend.form.port'), 'class' => 'form-control' . ($errors->has('port') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('port'))
                                        <div class="invalid-feedback">{{ $errors->first('port') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('timeout', __('frontend.form.timeout') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('timeout', old('timeout', $row->timeout ?? 5), ['placeholder' => __('frontend.form.timeout'), 'class' => 'form-control' . ($errors->has('timeout') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('timeout'))
                                        <div class="invalid-feedback">{{ $errors->first('timeout') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('secure', __('frontend.form.secure_connection'), ['class' => 'form-label']) !!}
                                    {!! form_select('secure', [
                                        'no' => __('frontend.str.no'),
                                        'ssl' => 'ssl',
                                        'tls' => 'tls',
                                    ], $selectedSecure, ['class' => 'form-select' . ($errors->has('secure') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('secure'))
                                        <div class="invalid-feedback">{{ $errors->first('secure') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('authentication', __('frontend.form.authentication_method'), ['class' => 'form-label']) !!}
                                    {!! form_select('authentication', [
                                        'login' => 'LOGIN (' . __('frontend.form.low_secrecy') . ')',
                                        'plain' => 'PLAIN (' . __('frontend.form.medium_secrecy') . ')',
                                        'cram-md5' => 'CRAM-MD5 (' . __('frontend.form.high_secrecy') . ')',
                                    ], $selectedAuthentication, ['class' => 'form-select' . ($errors->has('authentication') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('authentication'))
                                        <div class="invalid-feedback">{{ $errors->first('authentication') }}</div>
                                    @endif
                                </div>
                            </div>

                            @if ($errors->has('connection'))
                                <div class="col-12">
                                    <div class="alert alert-danger mb-0">{{ $errors->first('connection') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row justify-content-start">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary btn-back" href="{{ route('admin.smtp.index') }}">
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
