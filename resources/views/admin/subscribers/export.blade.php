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
                            <i class="fas fa-file-export me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    {!! form_open(['url' => route('admin.subscribers.export_subscribers'), 'method' => 'post']) !!}

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    {!! form_label('categoryId[]', __('frontend.form.subscribers_category'), ['class' => 'form-label']) !!}
                                    {!! form_select('categoryId[]', $options, old('categoryId'), ['multiple' => 'multiple', 'placeholder' => __('frontend.form.select_category'), 'class' => 'form-select' . ($errors->has('categoryId') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('categoryId'))
                                        <p class="text-danger mb-0 mt-1">{{ $errors->first('categoryId') }}</p>
                                    @endif
                                </div>

                                <div class="border rounded p-3 bg-body-tertiary mb-3">
                                    <div class="fw-semibold mb-2">
                                        <i class="fas fa-file-alt me-1"></i>
                                        {{ __('frontend.form.format') }}
                                    </div>

                                    <div class="form-check mb-2">
                                        {!! form_radio('export_type', 'text', old('export_type', 'text') === 'text', ['class' => 'form-check-input', 'id' => 'export_type_text']) !!}
                                        {!! form_label('export_type_text', __('frontend.form.text'), ['class' => 'form-check-label']) !!}
                                    </div>

                                    <div class="form-check">
                                        {!! form_radio('export_type', 'excel', old('export_type') === 'excel', ['class' => 'form-check-input', 'id' => 'export_type_excel']) !!}
                                        {!! form_label('export_type_excel', 'MS Excel', ['class' => 'form-check-label']) !!}
                                    </div>

                                    @if ($errors->has('export_type'))
                                        <p class="text-danger mb-0 mt-2">{{ $errors->first('export_type') }}</p>
                                    @endif
                                </div>

                                <div class="border rounded p-3 bg-body-tertiary">
                                    <div class="fw-semibold mb-2">
                                        <i class="fas fa-file-archive me-1"></i>
                                        {{ __('frontend.form.compress') }}
                                    </div>

                                    <div class="form-check mb-2">
                                        {!! form_radio('compress', 'none', old('compress', 'none') === 'none', ['class' => 'form-check-input', 'id' => 'compress_none']) !!}
                                        {!! form_label('compress_none', __('frontend.str.no'), ['class' => 'form-check-label']) !!}
                                    </div>

                                    <div class="form-check">
                                        {!! form_radio('compress', 'zip', old('compress') === 'zip', ['class' => 'form-check-input', 'id' => 'compress_zip']) !!}
                                        {!! form_label('compress_zip', 'zip', ['class' => 'form-check-label']) !!}
                                    </div>

                                    @if ($errors->has('compress'))
                                        <p class="text-danger mb-0 mt-2">{{ $errors->first('compress') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex flex-column flex-sm-row gap-2 justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>
                            {{ __('frontend.form.send') }}
                        </button>

                        <a class="btn btn-secondary" href="{{ route('admin.subscribers.index') }}">
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
