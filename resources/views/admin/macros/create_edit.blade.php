@extends('admin.app')

@section('title', $title)

@section('css')


@endsection

@section('content')

    <!-- Main content -->
    <section class="content">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- general form elements -->
                    <header class="card card-primary">

                        <!-- form start -->
                        {!! form_open(['url' => isset($row) ? route('admin.macros.update') : route('admin.macros.store'), 'method' => isset($row) ? 'put' : 'post']) !!}

                        {!! isset($row) ? form_hidden('id', $row->id) : '' !!}

                        <div class="card-body">

                            <p>*-{{ __('frontend.form.required_fields') }}</p>

                            <div class="form-group">
                                {!! form_label('name', __('frontend.form.macros_name') . '*') !!}

                                {!! form_text('name', old('name', $row->name ?? null), ['class' => 'form-control', 'placeholder' => __('frontend.form.name')]) !!}

                                @if ($errors->has('name'))
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                @endif
                            </div>

                            <div class="form-group">

                                {!! form_label('value', __('frontend.form.value') . '*') !!}

                                {!! form_textarea('value', old('value', $row->value ?? null), [ 'placeholder' => __('frontend.form.value'), 'rows' => 3, 'class' => 'form-control']) !!}

                                @if ($errors->has('value'))
                                    <p class="text-danger">{{ $errors->first('value') }}</p>
                                @endif

                            </div>

                            <div class="form-group">

                                {!! form_label('type', __('frontend.form.macros_type') . '*') !!}

                                {!! form_select('type', $options, $row->type ?? null, ['placeholder' => __('frontend.form.macros_type'), 'class' => 'form-select']) !!}

                                @if ($errors->has('type'))
                                    <p class="text-danger">{{ $errors->first('type') }}</p>
                                @endif
                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                            </button>
                            <a class="btn btn-secondary float-sm-end" href="{{ route('admin.macros.index') }}">
                                {{ __('frontend.form.back') }}
                            </a>
                        </div>

                        {!! form_close() !!}

                    </header>

                </div>
                <!-- /.card -->
            </div>
        </div>

    </section>
    <!-- /.content -->

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

