@extends('admin.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

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

                    {!! form_open(['url' => isset($row) ? route('admin.category.update') : route('admin.category.store'), 'method' => isset($row) ? 'put' : 'post']) !!}

                    {!! isset($row) ? form_hidden('id', $row->id) : '' !!}

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('name', __('frontend.form.name') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('name', old('name', $row->name ?? null), ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => __('frontend.form.name')]) !!}

                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex flex-column flex-sm-row gap-2 justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary" href="{{ route('admin.category.index') }}">
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
