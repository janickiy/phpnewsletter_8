@extends('admin.app')

@section('title', $title)

@section('css')


@endsection

@section('content')

    <!-- Main content -->
    <section class="content">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- general form elements -->
                    <header class="card card-primary">

                        <!-- form start -->
                        {!! form_open(['url' => isset($row) ? route('admin.smtp.update') : route('admin.smtp.store'), 'method' => isset($row) ? 'put' : 'post']) !!}

                        {!! isset($row) ? form_hidden('id', $row->id) : '' !!}

                        <div class="card-body">

                            <p>*-{{ __('frontend.form.required_fields') }}</p>

                            <div class="form-group">
                                {!! form_label('host', __('frontend.form.smtp_server') . '*') !!}

                                {!! form_text('host', old('host', $row->host ?? null), [ 'placeholder' => __('frontend.form.smtp_server'), 'class' => 'form-control']) !!}

                                @if ($errors->has('host'))
                                    <p class="text-danger">{{ $errors->first('host') }}</p>
                                @endif
                            </div>

                            <div class="form-group">
                                {!! form_label('email', 'E-mail*', ['class' => 'label']) !!}

                                {!! form_text('email', old('email', $row->email ?? null), [ 'placeholder' => 'E-mail', 'class' => 'form-control']) !!}

                                @if ($errors->has('email'))
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                @endif
                            </div>

                            <div class="form-group">
                                {!! form_label('username', __('frontend.form.login') . '*') !!}

                                {!! form_text('username', old('username', $row->username ?? null), [ 'placeholder' => __('frontend.form.login'), 'class' => 'form-control']) !!}

                                @if ($errors->has('username'))
                                    <p class="text-danger">{{ $errors->first('username') }}</p>
                                @endif
                            </div>

                            <div class="form-group">

                                {!! form_label('password', __('frontend.form.password')) !!}

                                {!! form_text('password', old('password', $row->password ?? null), [ 'placeholder' => __('frontend.form.password'), 'class' => 'form-control']) !!}

                                @if ($errors->has('password'))
                                    <p class="text-danger">{{ $errors->first('password') }}</p>
                                @endif
                            </div>

                            <div class="form-group">
                                {!! form_label('port', __('frontend.form.port') . '*') !!}

                                {!! form_text('port', old('port', $row->port ?? 25), [ 'placeholder' => __('frontend.form.port'), 'class' => 'form-control']) !!}

                                @if ($errors->has('port'))
                                    <p class="text-danger">{{ $errors->first('port') }}</p>
                                @endif
                            </div>

                            <div class="form-group">
                                {!! form_label('timeout', __('frontend.form.timeout') . '*') !!}

                                {!! form_text('timeout', old('timeout', $row->timeout ?? 5), [ 'placeholder' => __('frontend.form.timeout'), 'class' => 'form-control']) !!}

                                @if ($errors->has('timeout'))
                                    <p class="text-danger">{{ $errors->first('timeout') }}</p>
                                @endif
                            </div>

                            <div class="form-group">
                                {!! form_label('secure', __('frontend.form.secure_connection')) !!}

                                <div class="inline-group">
                                    <label class="radio">

                                        {!! form_radio('secure', 'no', old('secure', ((isset($row) && $row->secure == 'no') || !isset($row)) ? true : false )) !!}

                                        <i></i>
                                        {{ __('frontend.str.no') }}</label>
                                    <label class="radio">

                                        {!! form_radio('secure', 'ssl', old('secure', (isset($row) && ($row->secure == 'ssl')) ? true : false )) !!}

                                        <i></i>ssl</label>
                                    <label class="radio">

                                        {!! form_radio('secure', 'tls', old('secure', (isset($row) && ($row->secure == 'tls')) ? true : false )) !!}

                                        <i></i>tls</label>
                                </div>

                            </div>

                            <div class="form-group">

                                {!! form_label('authentication', __('frontend.form.authentication_method')) !!}

                                <div class="inline-group">
                                    <label class="radio">

                                        {!! form_radio('authentication', 'no', old('authentication', ((isset($row) && $row->authentication == 'no') || !isset($row)) ? true : false )) !!}

                                        <i></i>LOGIN ({{ __('frontend.form.low_secrecy') }})</label>
                                    <label class="radio">

                                        {!! form_radio('authentication', 'plain', old('authentication' , (isset($row) && ($row->authentication == 'plain')) ? true : false )) !!}

                                        <i></i>PLAIN ({{ __('frontend.form.medium_secrecy') }})</label>
                                    <label class="radio">

                                        {!! form_radio('authentication', 'crammd5', old('authentication' , (isset($row) && ($row->authentication == 'crammd5')) ? true : false )) !!}

                                        <i></i>CRAM-MD5 ({{ __('frontend.form.high_secrecy') }})</label>
                                </div>

                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                            </button>
                            <a class="btn btn-secondary float-sm-end" href="{{ route('admin.smtp.index') }}">
                                {{ __('frontend.form.back') }}
                            </a>
                        </div>

                    {!! form_close() !!}

                    </header>

                </div>
                <!-- /.card -->
            </div>
        </div>

    </section>
    <!-- /.content -->

@endsection

@section('js')


@endsection
