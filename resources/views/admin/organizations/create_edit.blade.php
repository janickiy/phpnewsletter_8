@extends('admin.app')

@section('title', $title)

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    {!! form_open([
                        'url' => isset($row)
                            ? route('admin.organizations.update', ['organization' => $row->id])
                            : route('admin.organizations.store'),
                        'method' => isset($row) ? 'put' : 'post',
                    ]) !!}

                    <div class="card-body">
                        <p>*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="form-group mb-3">
                            {!! form_label('name', __('frontend.form.name') . '*') !!}
                            {!! form_text('name', old('name', $row->name ?? null), ['class' => 'form-control', 'placeholder' => __('frontend.form.name')]) !!}

                            @if ($errors->has('name'))
                                <p class="text-danger">{{ $errors->first('name') }}</p>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            {!! form_label('description', __('frontend.form.description')) !!}
                            {!! form_textarea('description', old('description', $row->description ?? null), ['class' => 'form-control', 'rows' => 4, 'placeholder' => __('frontend.form.description')]) !!}

                            @if ($errors->has('description'))
                                <p class="text-danger">{{ $errors->first('description') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row gap-2 justify-content-start">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary btn-back" href="{{ isset($row) ? route('admin.organizations.show', ['organization' => $row->id]) : route('admin.organizations.index') }}">
                            {{ __('frontend.form.back') }}
                        </a>
                    </div>

                    {!! form_close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection
