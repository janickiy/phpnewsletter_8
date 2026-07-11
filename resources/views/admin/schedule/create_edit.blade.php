@extends('admin.app')

@section('title', $title)

@section('breadcrumbs')
    <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard.index') }}">{{ __('frontend.str.admin_panel') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.schedule.index') }}">{{ __('frontend.menu.schedule') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ $title }}</li>
    </ol>
@endsection

@section('css')

    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">

@endsection

@section('content')
    @php
        $selectedCategoryIds = collect(old('categoryId', $categoryId ?? []))
            ->map(fn ($value) => (string) $value)
            ->all();
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas {{ isset($row) ? 'fa-pen' : 'fa-calendar-alt' }} me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    {!! form_open(['url' => isset($row) ? route('admin.schedule.update') : route('admin.schedule.store'), 'method' => isset($row) ? 'put' : 'post']) !!}

                    {!! isset($row) ? form_hidden('id', $row->id) : '' !!}

                    <div class="card-body">
                        <p class="text-muted small mb-3">*-{{ __('frontend.form.required_fields') }}</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('event_name', __('frontend.form.name') . '*', ['class' => 'form-label']) !!}
                                    {!! form_text('event_name', old('event_name', $row->event_name ?? null), ['class' => 'form-control' . ($errors->has('event_name') ? ' is-invalid' : ''), 'placeholder' => __('frontend.form.name')]) !!}

                                    @if ($errors->has('event_name'))
                                        <div class="invalid-feedback">{{ $errors->first('event_name') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('template_id', __('frontend.form.template'), ['class' => 'form-label']) !!}
                                    {!! form_select('template_id', $options, old('template_id', $row->template_id ?? null), [
                                        'placeholder' => __('frontend.form.select'),
                                        'class' => 'form-select js-live-search-select' . ($errors->has('template_id') ? ' is-invalid' : ''),
                                        'data-search-placeholder' => __('frontend.form.search'),
                                        'data-no-results' => __('pagination.s_zero_records'),
                                    ]) !!}

                                    @if ($errors->has('template_id'))
                                        <div class="invalid-feedback">{{ $errors->first('template_id') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('date_interval', __('frontend.str.date'), ['class' => 'form-label']) !!}
                                    <div class="input-group has-validation">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                        {!! form_text('date_interval', old('date_interval', $date_interval ?? null), ['placeholder' => 'DD.MM.YYYY HH:MM - DD.MM.YYYY HH:MM', 'class' => 'form-control' . ($errors->has('date_interval') ? ' is-invalid' : ''), 'id' => 'date_interval']) !!}

                                        @if ($errors->has('date_interval'))
                                            <div class="invalid-feedback">{{ $errors->first('date_interval') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    {!! form_label('categoryId', __('frontend.form.subscribers_category'), ['class' => 'form-label']) !!}

                                    <select name="categoryId[]" id="categoryId" multiple class="form-select{{ $errors->has('categoryId') ? ' is-invalid' : '' }}">
                                        @foreach($category_options as $categoryValue => $categoryLabel)
                                            <option value="{{ $categoryValue }}" @selected(in_array((string) $categoryValue, $selectedCategoryIds, true))>
                                                {{ $categoryLabel }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('categoryId'))
                                        <div class="invalid-feedback">{{ $errors->first('categoryId') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row justify-content-start">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($row) ? __('frontend.form.edit') : __('frontend.form.add') }}
                        </button>

                        <a class="btn btn-secondary btn-back" href="{{ route('admin.schedule.index') }}">
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

    <!-- moment -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>

    {{-- Динамическое подключение locale --}}
    @php
        $localeMap = [
            'ru' => 'ru',
            'en' => 'en-gb', // важно: у moment нет просто "en"
            'uk' => 'uk',
            'de' => 'de',
            'fr' => 'fr',
            'es' => 'es',
            'it' => 'it',
            'hi' => 'hi',
            'pt' => 'pt',
            'pt-BR' => 'pt-br',
            'zh-CN' => 'zh-cn',
            'zh-TW' => 'zh-tw',
        ];

        $momentLocale = $localeMap[app()->getLocale()] ?? 'en-gb';
    @endphp

    <script src="{{ asset('/plugins/moment/locale/' . $momentLocale . '.js') }}"></script>

    <!-- daterangepicker -->
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

    <script>
        $(function () {

            let locale = @json($momentLocale);

            moment.locale(locale);

            let localeData = moment.localeData();

            $('#date_interval').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                timePicker24Hour: true,
                locale: {
                    format: 'DD.MM.YYYY HH:mm',
                    separator: ' - ',
                    applyLabel: @json(__('frontend.str.apply')),
                    cancelLabel: @json(__('frontend.str.cancel')),
                    daysOfWeek: localeData.weekdaysMin(),
                    monthNames: localeData.months(),
                    firstDay: localeData.firstDayOfWeek()
                },
                minDate: moment().add(1, 'days'),
                maxDate: moment().add(359, 'days'),
            });

        });
    </script>

@endsection
