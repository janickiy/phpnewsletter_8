<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === "ar" ? "rtl" : "ltr" }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP Newsletter | {{ __('frontend.title.auth') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ url('favicon.ico') }}" type="image/x-icon">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome7/css/all.min.css') }}">

    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte4/css/adminlte.min.css') }}">

    <style>
        .auth-logo {
            width: min(300px, 100%);
            height: auto;
        }

        .login-box .card-header {
            padding: 1.25rem 1.25rem 1rem;
        }
    </style>

</head>
<body class="login-page bg-body-secondary">
<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">

            <img src="{{ url('/dist/img/logo-auth-install.png') }}?v={{ filemtime(public_path('dist/img/logo-auth-install.png')) }}" alt="PHP Newsletter" class="auth-logo">
        </div>
        <div class="card-body">
            <p class="login-box-msg">{{ __('auth.admin_area') }}</p>


            {!! form_open(['url' => route('login'), 'method' => 'post']) !!}


                <div class="input-group mb-3">
                    {!! form_text('login', old('login'), [ 'placeholder' => __('frontend.form.login'), 'class' => 'form-control']) !!}
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
                @if ($errors->has('login'))
                    <p class="text-danger">{{ $errors->first('login') }}</p>
                @endif

                <div class="input-group mb-3">
                    {!! form_password('password',['class' => 'form-control', 'placeholder' => __('frontend.form.password'), 'type' => 'password']) !!}
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                </div>
                @if ($errors->has('password'))
                    <p class="text-danger">{{ $errors->first('password') }}</p>
                @endif

                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">

                            {!! form_checkbox('remember', 1, old('remember') ? true : false , ['id' => "remember"]) !!}

                            <label for="remember">
                                {{ __('frontend.str.remember_me') }}
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        {!! form_submit(__('frontend.str.singin'), ['class' => 'btn btn-primary w-100']) !!}
                    </div>
                    <!-- /.col -->
                </div>

            {!! form_close() !!}




        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 5 -->
<script src="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('vendor/adminlte4/js/adminlte.min.js') }}"></script>
</body>
</html>
