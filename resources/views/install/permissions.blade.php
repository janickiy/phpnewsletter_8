@extends('layouts.install')

@section('content')

    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected'
    ]])

    <div class="step-content">
        <h3>{{ __('install.str.access_rights') }}</h3>
        <hr>
        <ul class="list-group mb-4">
            @foreach($folders as $path => $isWritable)
                <li class="list-group-item">
                    {{ $path }}
                    @if ($isWritable)
                        <span class="badge text-bg-secondary float-end ms-2">775</span>
                        <span class="badge text-bg-success float-end"><i class="fa fa-check"></i></span>
                    @else
                        <span class="badge text-bg-secondary float-end ms-2">775</span>
                        <span class="badge text-bg-danger float-end"><i class="fa fa-times"></i></span>
                    @endif
                </li>
            @endforeach
        </ul>
        <a class="btn btn-primary float-end" href="{{ route('install.database') }}">
            {{ __('install.button.next') }}
            <i class="fa fa-arrow-right"></i>
        </a>
        <div class="clearfix"></div>
    </div>

@endsection

@section('js')

@endsection
