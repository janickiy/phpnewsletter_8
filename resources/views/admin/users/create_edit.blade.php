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
                            <i class="fas {{ isset($row) ? 'fa-user-edit' : 'fa-user-plus' }} me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    {!! form_open(['url' => isset($row) ? route('admin.users.update') : route('admin.users.store'), 'method' => isset($row) ? 'put' : 'post']) !!}

                    {!! isset($row) ? form_hidden('id', $row->id) : '' !!}

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('name', __('frontend.form.name') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('name', old('name', $row->name ?? null), ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => __('frontend.form.name')]) !!}

                                    @if ($errors->has('name'))
                                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('login', __('frontend.form.login') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('login', old('login', $row->login ?? null), ['placeholder' => __('frontend.form.login'), 'class' => 'form-control' . ($errors->has('login') ? ' is-invalid' : ''), 'autocomplete' => 'username']) !!}

                                    @if ($errors->has('login'))
                                        <div class="invalid-feedback">{{ $errors->first('login') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('password', __('frontend.form.password') . (!isset($row) ? '*' : ''), ['class' => 'form-label']) !!}
                                    {!! form_password('password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'autocomplete' => 'new-password']) !!}

                                    @if ($errors->has('password'))
                                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                    @endif

                                    @if (isset($row))
                                        <small class="form-text text-muted">
                                            {{ __('frontend.form.leave_blank_password') }}
                                        </small>
                                    @endif
                                </div>
                            </div>

                            @if ((isset($row->id) && $row->id != Auth::user()->id) || !isset($row->id))
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        {!! form_label('role', __('frontend.form.role') . '*', ['class' => 'form-label']) !!}
                                        {!! form_select('role', $options, old('role', $row->role ?? 'admin'), ['placeholder' => __('frontend.form.select_role'), 'class' => 'form-select' . ($errors->has('role') ? ' is-invalid' : '')]) !!}

                                        @if ($errors->has('role'))
                                            <div class="invalid-feedback">{{ $errors->first('role') }}</div>
                                        @endif

                                        <div class="form-text text-muted mt-2">
                                            <strong>{{ __('frontend.form.roles_note_title') }}</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>{{ __('frontend.form.role_note_admin') }}</li>
                                                <li>{{ __('frontend.form.role_note_project_admin') }}</li>
                                                <li>{{ __('frontend.form.role_note_moderator') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {!! form_hidden('role', $row->role) !!}
                            @endif

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('password_again', __('frontend.form.password_again') . (!isset($row) ? '*' : ''), ['class' => 'form-label']) !!}
                                    {!! form_password('password_again', ['class' => 'form-control' . ($errors->has('password_again') ? ' is-invalid' : ''), 'autocomplete' => 'new-password']) !!}

                                    @if ($errors->has('password_again'))
                                        <div class="invalid-feedback">{{ $errors->first('password_again') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('description', __('frontend.form.description'), ['class' => 'form-label']) !!}
                                    {!! form_textarea('description', old('description', $row->description ?? null), ['placeholder' => __('frontend.form.description'), 'rows' => 5, 'class' => 'form-control' . ($errors->has('description') ? ' is-invalid' : '')]) !!}

                                    @if ($errors->has('description'))
                                        <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row justify-content-start">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary btn-back" href="{{ route('admin.users.index') }}">
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
