@extends('layouts.install')

@section('content')

    @include('install.steps', ['steps' => ['welcome' => 'selected done', 'requirements' => 'selected']])

    @if (! $allLoaded)
        <div class="alert alert-danger">
            <strong>Oh snap!</strong> Your system does not meet the requirements. You have to fix them in order to continue.
        </div>
    @endif

    <div class="step-content">
        <h3>{{ __('install.str.system_requirements') }}</h3>
        <hr>
        <ul class="list-group mb-4">
            @foreach ($requirements as $extension => $loaded)
                <li class="list-group-item {{ ! $loaded ? 'list-group-item-danger' : '' }}">
                    {{ $extension }}
                    @if ($loaded)
                        <span class="badge text-bg-success float-end"><i class="fa fa-check"></i></span>
                    @else
                        <span class="badge text-bg-danger float-end"><i class="fa fa-times"></i></span>
                    @endif
                </li>
            @endforeach
        </ul>
        @if ($allLoaded)
            <a class="btn btn-primary float-end" href="{{ route('install.permissions') }}">
                {{ __('install.button.next') }}
                <i class="fa fa-arrow-right"></i>
            </a>
        @else
            <button class="btn btn-primary float-end" disabled>
                {{ __('install.button.next') }}
                <i class="fa fa-arrow-right"></i>
            </button>
        @endif
        <div class="clearfix"></div>
    </div>

@endsection

@section('js')

@endsection
