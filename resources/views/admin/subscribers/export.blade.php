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
                        {!! form_open(['url' => route('admin.subscribers.export_subscribers'), 'method' =>'post']) !!}

                        <div class="card-body">

                            <p>*-{{ __('frontend.form.required_fields') }}</p>

                            <div class="form-group">

                                {!! form_label('export_type', __('frontend.form.format')) !!}

                                <div class="inline-group">
                                    <label class="radio">

                                        {!! form_radio('export_type', 'text', true) !!}

                                        <i></i>{{ __('frontend.form.text') }}
                                    </label>
                                    <label class="radio">

                                        {!! form_radio('export_type', 'excel', false) !!}

                                        <i></i>MS Excel
                                    </label>
                                </div>

                                @if ($errors->has('name'))
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                @endif
                            </div>

                            <div class="form-group">

                                {!! form_label('compress', __('frontend.form.format')) !!}

                                <div class="inline-group">
                                    <label class="radio">

                                        {!! form_radio('compress', 'none', true) !!}

                                        <i></i>{{ __('frontend.str.no') }}
                                    </label>
                                    <label class="radio">

                                        {!! form_radio('compress', 'zip', false) !!}

                                        <i></i>zip
                                    </label>
                                </div>

                            </div>

                            <div class="form-group">

                                {!! form_label('categoryId[]', __('frontend.form.subscribers_category')) !!}

                                {!! form_select('categoryId[]', $options, null, ['multiple'=>'multiple', 'placeholder' => __('frontend.form.select_category'), 'class' => 'form-control']) !!}

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
