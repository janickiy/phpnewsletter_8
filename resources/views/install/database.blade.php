@extends('layouts.install')

@section('content')

    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected done',
        'database' => 'selected'
    ]])

    @include('layouts.notifications')

    {!! form_open(['route' => 'install.installation']) !!}

    <div class="step-content">
        <h3>{{ __('install.str.database_information') }}</h3>
        <hr>
        <div class="form-group">

            {!! form_label('host', __('install.str.database_host')) !!}

            {!! form_text('host', old('host'), ['class' => "form-control", 'placeholder' => "",'id' => "host"]) !!}

            <small>{{ __('install.hint.database_host') }}</small>
            @if ($errors->has('host'))
                <p class="text-danger">{{ $errors->first('host') }}</p>
            @endif
        </div>
        <div class="form-group">

            {!! form_label('username', __('install.str.database_username')) !!}

            {!! form_text('username', old('username'), ['class' => "form-control", 'placeholder' => "",'id' => "username"]) !!}

            <small>{{ __('install.hint.database_username') }}</small>
            @if ($errors->has('username'))
                <p class="text-danger">{{ $errors->first('username') }}</p>
            @endif
        </div>
        <div class="form-group">

            {!! form_label('password', __('install.str.password')) !!}

            {!! form_password('password', ['class' => "form-control", 'id' => "password"]) !!}

            <small>{{ __('install.hint.database_password') }}</small>
            @if ($errors->has('password'))
                <p class="text-danger">{{ $errors->first('password') }}</p>
            @endif
        </div>
        <div class="form-group">
            {!! form_label('database', __('install.str.database_name')) !!}

            {!! form_text('database', old('database'), ['class' => "form-control", 'placeholder' => "",'id' => "database"]) !!}

            <small>{{ __('install.hint.database_name') }}</small>
            @if ($errors->has('database'))
                <p class="text-danger">{{ $errors->first('database') }}</p>
            @endif
        </div>

        <button class="btn btn-primary float-end mt-3">
            {{ __('install.button.next') }}
            <i class="fa fa-arrow-right"></i>
        </button>
        <div class="clearfix"></div>
    </div>

    {!! form_close() !!}

@endsection

@section('js')

@endsection
