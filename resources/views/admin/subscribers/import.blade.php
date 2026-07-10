@extends('admin.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-import me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    {!! form_open(['url' => route('admin.subscribers.import_subscribers'), 'files' => true, 'method' => 'post']) !!}

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="form-group mb-3">
                                    {!! form_label('import', __('frontend.form.attach_files') . '*', ['class' => 'form-label']) !!}

                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="fas fa-file-upload"></i></span>
                                        {!! form_file('import',  ['id' => 'import', 'class' => 'form-control' . ($errors->has('import') ? ' is-invalid' : ''), 'accept' => '.csv,.xlsx,.xls,.ods,.txt']) !!}

                                        @if ($errors->has('import'))
                                            <div class="invalid-feedback">{{ $errors->first('import') }}</div>
                                        @endif
                                    </div>

                                    <div class="form-text">
                                        {{ __('frontend.form.maximum_size') }}: {{ $maxUploadFileSize }}
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    {!! form_label('categoryId[]', __('frontend.form.subscribers_category'), ['class' => 'form-label']) !!}
                                    {!! form_select('categoryId[]', $category_options, old('categoryId'), ['multiple' => 'multiple', 'placeholder' => __('frontend.form.select_category'), 'class' => 'form-select' . ($errors->has('categoryId') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('categoryId'))
                                        <p class="text-danger mb-0 mt-1">{{ $errors->first('categoryId') }}</p>
                                    @endif
                                </div>

                                <div class="form-group mb-0">
                                    {!! form_label('charset', __('frontend.form.charset'), ['class' => 'form-label']) !!}
                                    {!! form_select('charset', $charsets, old('charset'), ['placeholder' => '--' . __('frontend.form.select') . '--', 'class' => 'form-select' . ($errors->has('charset') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('charset'))
                                        <p class="text-danger mb-0 mt-1">{{ $errors->first('charset') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="border rounded p-3 bg-body-tertiary">
                                    <div class="fw-semibold mb-2">
                                        <i class="fas fa-file-csv me-1"></i>
                                        {{ __('frontend.form.format') }}
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge text-bg-light border">CSV</span>
                                        <span class="badge text-bg-light border">XLSX</span>
                                        <span class="badge text-bg-light border">XLS</span>
                                        <span class="badge text-bg-light border">TXT</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row justify-content-start">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>
                            {{ __('frontend.form.send') }}
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
