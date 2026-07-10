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

                    {!! form_open(['url' => isset($row) ? route('admin.macros.update') : route('admin.macros.store'), 'method' => isset($row) ? 'put' : 'post']) !!}

                    {!! isset($row) ? form_hidden('id', $row->id) : '' !!}

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('name', __('frontend.form.macros_name') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('name', old('name', $row->name ?? null), ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => __('frontend.form.name')]) !!}

                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('type', __('frontend.form.macros_type') . '*', ['class' => 'form-label']) !!}
                                    {!! form_select('type', $options, old('type', $row->type ?? null), ['placeholder' => __('frontend.form.macros_type'), 'class' => 'form-select' . ($errors->has('type') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('type'))
                                        <div class="invalid-feedback">{{ $errors->first('type') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0">
                                    {!! form_label('value', __('frontend.form.value') . '*', ['class' => 'form-label']) !!}
                                    {!! form_textarea('value', old('value', $row->value ?? null), ['placeholder' => __('frontend.form.value'), 'rows' => 4, 'class' => 'form-control' . ($errors->has('value') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('value'))
                                        <div class="invalid-feedback">{{ $errors->first('value') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row gap-2 justify-content-start">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary btn-back" href="{{ route('admin.macros.index') }}">
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

    <script>
        $(function () {
            $('#type').on('change', function () {
                let sampleMacros = getValue(this.value);
                $('#value').val(sampleMacros);
            });
        });

        function getValue(value) {
            switch (value) {
                case '1':
                    return '{{ __('frontend.form.sample_macros_type_url') }}';
                case '2':
                    return '{{ __('frontend.form.sample_macros_type_email') }}';
                case '3':
                    return '{{ __('frontend.form.sample_macros_type_hash_tags') }}';
                case '4':
                    return '{{ __('frontend.form.sample_macros_type_tags') }}';
                case '5':
                    return '{{ __('frontend.form.sample_macros_type_wrap_phrase') }}';
            }
        }

    </script>

@endsection
