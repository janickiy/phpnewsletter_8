@extends('admin.app')

@section('title', $title)

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-folder-open me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    {!! form_open([
                        'url' => isset($row)
                            ? route('admin.projects.update', ['organization' => $organization->id, 'project' => $row->id])
                            : route('admin.projects.store', ['organization' => $organization->id]),
                        'method' => isset($row) ? 'put' : 'post',
                    ]) !!}

                    <div class="card-body">
                        <p>*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    {!! form_label('name', __('frontend.form.name') . '*') !!}
                                    {!! form_text('name', old('name', $row->name ?? null), ['class' => 'form-control', 'placeholder' => __('frontend.form.name')]) !!}

                                    @if ($errors->has('name'))
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    {!! form_label('status', __('frontend.str.status') . '*') !!}
                                    {!! form_select('status', $statusOptions, old('status', $row->status ?? \App\Models\Project::STATUS_ACTIVE), ['class' => 'form-select']) !!}

                                    @if ($errors->has('status'))
                                        <p class="text-danger">{{ $errors->first('status') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    {!! form_label('default_sender_name', __('frontend.str.default_sender_name')) !!}
                                    {!! form_text('default_sender_name', old('default_sender_name', $row->default_sender_name ?? null), ['class' => 'form-control', 'placeholder' => __('frontend.str.default_sender_name')]) !!}

                                    @if ($errors->has('default_sender_name'))
                                        <p class="text-danger">{{ $errors->first('default_sender_name') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    {!! form_label('default_from_email', __('frontend.str.default_from_email')) !!}
                                    {!! form_text('default_from_email', old('default_from_email', $row->default_from_email ?? null), ['class' => 'form-control', 'placeholder' => 'mail@example.com']) !!}

                                    @if ($errors->has('default_from_email'))
                                        <p class="text-danger">{{ $errors->first('default_from_email') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    {!! form_label('default_reply_to', __('frontend.str.default_reply_to')) !!}
                                    {!! form_text('default_reply_to', old('default_reply_to', $row->default_reply_to ?? null), ['class' => 'form-control', 'placeholder' => 'reply@example.com']) !!}

                                    @if ($errors->has('default_reply_to'))
                                        <p class="text-danger">{{ $errors->first('default_reply_to') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    {!! form_label('timezone', __('frontend.str.timezone')) !!}
                                    {!! form_select('timezone', $timezoneOptions, old('timezone', $row->timezone ?? null), ['placeholder' => __('frontend.form.select'), 'class' => 'form-select']) !!}

                                    @if ($errors->has('timezone'))
                                        <p class="text-danger">{{ $errors->first('timezone') }}</p>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="form-group mb-3">
                            {!! form_label('description', __('frontend.form.description')) !!}
                            {!! form_textarea('description', old('description', $row->description ?? null), ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('frontend.form.description')]) !!}

                            @if ($errors->has('description'))
                                <p class="text-danger">{{ $errors->first('description') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row gap-2 justify-content-start">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary btn-back" href="{{ route('admin.organizations.show', ['organization' => $organization->id]) }}">
                            {{ __('frontend.form.back') }}
                        </a>
                    </div>

                    {!! form_close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection
