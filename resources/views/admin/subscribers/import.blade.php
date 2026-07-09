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
                        {!! form_open(['url' => route('admin.subscribers.import_subscribers'), 'files' => true, 'method' => 'post']) !!}

                        <div class="card-body">

                            <p>*-{{ __('frontend.form.required_fields') }}</p>

                            <div class="form-group">

                                {!! form_label('import', __('frontend.form.attach_files') . '*') !!}

                                {!! form_file('import',  ['id' => 'import', 'class' => 'form-control', 'accept' => '.csv,.xlsx,.xls,.ods,.txt']) !!}

                                @if ($errors->has('import'))
                                    <p class="text-danger">{{ $errors->first('import') }}</p>
                                @endif

                                <blockquote class="quote-secondary">
                                    <small>{{ __('frontend.form.maximum_size') }}: <cite
                                            title="Source Title">{{ $maxUploadFileSize }}</cite></small>
                                </blockquote>

                            </div>

                            <div class="form-group">

                                {!! form_label('categoryId[]', __('frontend.form.charset')) !!}

                                {!! form_select('charset', $charsets, null, ['placeholder' => '--' . __('frontend.form.select') . '--', 'class' => 'form-control']) !!}

                                @if ($errors->has('charset'))
                                    <p class="text-danger">{{ $errors->first('charset') }}</p>
                                @endif

                            </div>

                            <div class="form-group">

                                {!! form_label('categoryId[]', __('frontend.form.subscribers_category')) !!}

                                {!! form_select('categoryId[]', $category_options, null, ['multiple' => 'multiple', 'placeholder' => __('frontend.form.select_category'), 'class' => 'form-control']) !!}

                                @if ($errors->has('categoryId'))
                                    <p class="text-danger">{{ $errors->first('categoryId') }}</p>
                                @endif

                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ __('frontend.form.send') }}
                            </button>
                            <a class="btn btn-secondary float-sm-end" href="{{ route('admin.subscribers.index') }}">
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

@endsection
