@extends('admin.app')

@section('title', $title)

@section('breadcrumbs')
    <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard.index') }}">{{ __('frontend.str.admin_panel') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.subscribers.index') }}">{{ __('frontend.menu.subscribers') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ $title }}</li>
    </ol>
@endsection

@section('css')

@endsection

@section('content')

    @php
        $selectedCategoryIds = collect((array) old('categoryId', $subscriberCategoryIds ?? []))
            ->map(fn ($value) => (string) $value)
            ->all();
        $selectedProjectIds = collect((array) old('projectId', $subscriberProjectIds ?? []))
            ->map(fn ($value) => (string) $value)
            ->all();
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas {{ isset($row) ? 'fa-user-edit' : 'fa-user-plus' }} me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    {!! form_open(['url' => isset($row) ? route('admin.subscribers.update') : route('admin.subscribers.store'), 'method' => isset($row) ? 'put' : 'post']) !!}

                    {!! isset($row) ? form_hidden('id', $row->id) : '' !!}

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('name', __('frontend.form.name'), ['class' => 'form-label']) !!}
                                    {!! form_text('name', old('name', $row->name ?? null), ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => __('frontend.form.name')]) !!}

                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('email', 'Email*', ['class' => 'form-label']) !!}
                                    {!! form_text('email', old('email', $row->email ?? null), ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'mail@example.com', 'autocomplete' => 'email']) !!}

                                    @if ($errors->has('email'))
                                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('categoryId', __('frontend.form.subscribers_category'), ['class' => 'form-label']) !!}

                                    <select name="categoryId[]" id="categoryId" multiple class="form-select{{ $errors->has('categoryId') ? ' is-invalid' : '' }}">
                                        @foreach($options as $categoryValue => $categoryLabel)
                                            <option value="{{ $categoryValue }}" @selected(in_array((string) $categoryValue, $selectedCategoryIds, true))>
                                                {{ $categoryLabel }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('categoryId'))
                                        <div class="invalid-feedback">{{ $errors->first('categoryId') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('projectId', __('frontend.str.project'), ['class' => 'form-label']) !!}

                                    <select name="projectId[]" id="projectId" multiple class="form-select{{ ($errors->has('projectId') || $errors->has('projectId.*')) ? ' is-invalid' : '' }}">
                                        @foreach($projectGroups as $projectGroup)
                                            @if($projectGroup['label'])
                                                <optgroup label="{{ $projectGroup['label'] }}">
                                                    @foreach($projectGroup['projects'] as $projectOption)
                                                        <option value="{{ $projectOption['id'] }}" @selected(in_array((string) $projectOption['id'], $selectedProjectIds, true))>
                                                            {{ $projectOption['name'] }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                @foreach($projectGroup['projects'] as $projectOption)
                                                    <option value="{{ $projectOption['id'] }}" @selected(in_array((string) $projectOption['id'], $selectedProjectIds, true))>
                                                        {{ $projectOption['name'] }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>

                                    @if ($errors->has('projectId') || $errors->has('projectId.*'))
                                        <div class="invalid-feedback">{{ $errors->first('projectId') ?: $errors->first('projectId.*') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row justify-content-start">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary btn-back" href="{{ route('admin.subscribers.index') }}">
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
