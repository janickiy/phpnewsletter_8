@extends('admin.app')

@section('title', $title)

@section('css')

    <style>
        .settings-page .form-group.row {
            margin-bottom: 1rem;
        }

        .settings-page .tab-pane > .form-group:last-child {
            margin-bottom: 0;
        }

        .settings-page .header-row + .header-row {
            margin-top: .75rem;
        }
    </style>

@endsection

@section('content')

    <div class="container-fluid settings-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    <div class="card-header bg-body-tertiary p-2">
                        <ul class="nav nav-pills flex-column flex-sm-row gap-1">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#s1" data-bs-toggle="tab">{{ __('frontend.str.interface_settings') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#s2" data-bs-toggle="tab">{{ __('frontend.str.mailing_options') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#s3" data-bs-toggle="tab">{{ __('frontend.str.additional_headers') }}</a>
                                </li>
                        </ul>
                    </div>

                    {!! form_open(['url' => route('admin.settings.update'), 'method' => 'put', 'class' => 'form-horizontal']) !!}

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="s1">
                                    <div class="form-group row">

                                        {!! form_label('EMAIL', __('frontend.str.sender_email'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('EMAIL', SettingsHelper::getInstance()->getValueForKey('EMAIL'), ['placeholder' => "Email", 'class' => 'form-control']) !!}

                                            @if ($errors->has('EMAIL'))
                                                <span class="text-danger">{{ $errors->first('EMAIL') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('FROM', __('frontend.str.sender_name'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('FROM', SettingsHelper::getInstance()->getValueForKey('FROM'), ['placeholder' => __("frontend.str.sender_name"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('FROM'))
                                                <span class="text-danger">{{ $errors->first('FROM') }}</span>
                                            @endif

                                        </div>
                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('RETURN_PATH', __('frontend.form.return_path'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('RETURN_PATH', SettingsHelper::getInstance()->getValueForKey('RETURN_PATH'), ['placeholder' => __("frontend.form.return_path"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('RETURN_PATH'))
                                                <span class="text-danger">{{ $errors->first('RETURN_PATH') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('LIST_OWNER', __('frontend.form.list_owner'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('LIST_OWNER', SettingsHelper::getInstance()->getValueForKey('LIST_OWNER'), ['placeholder' => __("frontend.form.list_owner"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('LIST_OWNER'))
                                                <span class="text-danger">{{ $errors->first('LIST_OWNER') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('ORGANIZATION', __('frontend.form.organization'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('ORGANIZATION', SettingsHelper::getInstance()->getValueForKey('ORGANIZATION'), ['placeholder' => __("frontend.form.organization"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('ORGANIZATION'))
                                                <span class="text-danger">{{ $errors->first('ORGANIZATION') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('SUBJECT_TEXT_CONFIRM', __('frontend.form.subject_text_confirm'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('SUBJECT_TEXT_CONFIRM', SettingsHelper::getInstance()->getValueForKey('SUBJECT_TEXT_CONFIRM'), ['placeholder' => __("frontend.form.subject_text_confirm"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('SUBJECT_TEXT_CONFIRM'))
                                                <span
                                                    class="text-danger">{{ $errors->first('SUBJECT_TEXT_CONFIRM') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('TEXT_CONFIRMATION', __('frontend.form.text_confirmation'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_textarea('TEXT_CONFIRMATION', SettingsHelper::getInstance()->getValueForKey('TEXT_CONFIRMATION'), ['rows' => "4", 'placeholder' => __("frontend.form.text_confirmation"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('TEXT_CONFIRMATION'))
                                                <span
                                                    class="text-danger">{{ $errors->first('TEXT_CONFIRMATION') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <div class="offset-sm-2 col-sm-10">
                                            <div class="form-check">

                                                {!! form_checkbox('REQUIRE_SUB_CONFIRMATION', 1, SettingsHelper::getInstance()->getValueForKey('REQUIRE_SUB_CONFIRMATION') == 1 ? true : false, ['class' => 'form-check-input']) !!}

                                                {!! form_label('REQUIRE_SUB_CONFIRMATION', __('frontend.form.require_subscription_confirmation'), ['class' => 'form-check-label']) !!}

                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('UNSUBLINK', __('frontend.form.unsublink_text'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_textarea('UNSUBLINK', SettingsHelper::getInstance()->getValueForKey('UNSUBLINK'), ['rows' => "4", 'placeholder' => __("frontend.form.unsublink_text"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('UNSUBLINK'))
                                                <span class="text-danger">{{ $errors->first('UNSUBLINK') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="s2">

                                    <div class="form-group row">

                                        <div class="offset-sm-2 col-sm-10">
                                            <div class="form-check">

                                                {!! form_checkbox('SHOW_UNSUBSCRIBE_LINK', 1, SettingsHelper::getInstance()->getValueForKey('SHOW_UNSUBSCRIBE_LINK') == 1 ? true : false, ['class' => 'form-check-input']) !!}

                                                {!! form_label('SHOW_UNSUBSCRIBE_LINK', __('frontend.form.show_unsubscribe_link'), ['class' => 'form-check-label']) !!}

                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <div class="offset-sm-2 col-sm-10">
                                            <div class="form-check">

                                                {!! form_checkbox('REQUEST_REPLY', 1, SettingsHelper::getInstance()->getValueForKey('REQUEST_REPLY') == 1 ? true : false, ['class' => 'form-check-input']) !!}

                                                {!! form_label('REQUEST_REPLY', __('frontend.form.request_reply'), ['class' => 'form-check-label']) !!}

                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <div class="offset-sm-2 col-sm-10">
                                            <div class="form-check">

                                                {!! form_checkbox('NEW_SUBSCRIBER_NOTIFY', 1, SettingsHelper::getInstance()->getValueForKey('NEW_SUBSCRIBER_NOTIFY') == 1 ? true : false, ['class' => 'form-check-input']) !!}

                                                {!! form_label('NEW_SUBSCRIBER_NOTIFY', __('frontend.form.new_subscriber_notify'), ['class' => 'form-check-label']) !!}

                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('INTERVAL_NUMBER', __('frontend.form.interval_number'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-md-7">

                                            {!! form_text('INTERVAL_NUMBER', SettingsHelper::getInstance()->getValueForKey('INTERVAL_NUMBER'), ['class' => 'form-control']) !!}

                                            @if ($errors->has('INTERVAL_NUMBER'))
                                                <span class="text-danger">{{ $errors->first('INTERVAL_NUMBER') }}</span>
                                            @endif

                                        </div>

                                        <div class="col-md-3">

                                            {!! form_select('INTERVAL_TYPE', [
                                                            'no' => __('frontend.str.no'),
                                                            'minute' => __('frontend.form.minute'),
                                                            'hour' => __('frontend.form.hour'),
                                                            'day' => __('frontend.form.day'),
                                                            ], SettingsHelper::getInstance()->getValueForKey('INTERVAL_TYPE') ? SettingsHelper::getInstance()->getValueForKey('INTERVAL_TYPE') : 'no', ['class' => 'form-select']
                                                            ) !!}

                                            @if ($errors->has('INTERVAL_TYPE'))
                                                <span class="text-danger">{{ $errors->first('INTERVAL_TYPE') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('LIMIT_NUMBER', __('frontend.form.limit_number'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    {!! form_checkbox('LIMIT_SEND', 1, SettingsHelper::getInstance()->getValueForKey('LIMIT_SEND') == 1 ? true : false) !!}
                                                </span>

                                                {!! form_text('LIMIT_NUMBER', SettingsHelper::getInstance()->getValueForKey('LIMIT_NUMBER'), ['class' => 'form-control']) !!}

                                                @if ($errors->has('LIMIT_NUMBER'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('LIMIT_NUMBER') }}</span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('SLEEP', __('frontend.form.sleep'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('SLEEP', !empty(SettingsHelper::getInstance()->getValueForKey('ORGANIZATION')) ?SettingsHelper::getInstance()->getValueForKey('ORGANIZATION'): 0, ['placeholder' => __("frontend.form.sleep"), 'class' => 'form-control']) !!}

                                            @if ($errors->has('SLEEP'))
                                                <span class="text-danger">{{ $errors->first('SLEEP') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('DAYS_FOR_REMOVE_SUBSCRIBER', __('frontend.form.days_for_remove_subscriber'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    {!! form_checkbox('REMOVE_SUBSCRIBER', 1, SettingsHelper::getInstance()->getValueForKey('REMOVE_SUBSCRIBER') == 1 ? true : false) !!}
                                                </span>

                                                {!! form_text('DAYS_FOR_REMOVE_SUBSCRIBER', SettingsHelper::getInstance()->getValueForKey('DAYS_FOR_REMOVE_SUBSCRIBER'), ['class' => 'form-control']) !!}

                                                @if ($errors->has('DAYS_FOR_REMOVE_SUBSCRIBER'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('DAYS_FOR_REMOVE_SUBSCRIBER') }}</span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <div class="offset-sm-2 col-sm-10">
                                            <div class="form-check">

                                                {!! form_checkbox('RANDOM_SEND', 1, SettingsHelper::getInstance()->getValueForKey('RANDOM_SEND') == 1 ? true : false, ['class' => 'form-check-input']) !!}

                                                {!! form_label('RANDOM_SEND', __('frontend.form.random_send'), ['class' => 'form-check-label']) !!}

                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <div class="offset-sm-2 col-sm-10">
                                            <div class="form-check">

                                                {!! form_checkbox('RENDOM_REPLACEMENT_SUBJECT', 1, SettingsHelper::getInstance()->getValueForKey('RENDOM_REPLACEMENT_SUBJECT') == 1 ? true : false, ['class' => 'form-check-input']) !!}

                                                {!! form_label('RENDOM_REPLACEMENT_SUBJECT', __('frontend.form.rendom_replacement_subject'), ['class' => 'form-check-label']) !!}

                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        <div class="offset-sm-2 col-sm-10">
                                            <div class="form-check">

                                                {!! form_checkbox('RANDOM_REPLACEMENT_BODY', 1, SettingsHelper::getInstance()->getValueForKey('RANDOM_REPLACEMENT_BODY') == 1 ? true : false, ['class' => 'form-check-input']) !!}

                                                {!! form_label('RANDOM_REPLACEMENT_BODY', __('frontend.form.random_replacement_body'), ['class' => 'form-check-label']) !!}

                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('PRECEDENCE', __('frontend.form.precedence'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_select('PRECEDENCE', [
                                                                             'no' => __('frontend.str.no'),
                                                                             'bulk' => 'bulk',
                                                                             'junk' => 'junk',
                                                                              'list' => 'list',
                                                                            ], SettingsHelper::getInstance()->getValueForKey('PRECEDENCE') ? SettingsHelper::getInstance()->getValueForKey('PRECEDENCE') : 'no', ['class' => 'form-select']
                                             ) !!}

                                            @if ($errors->has('CHARSET'))
                                                <span class="text-danger">{{ $errors->first('CHARSET') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('CHARSET', __('frontend.form.charset'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_select('CHARSET', $option_charset, SettingsHelper::getInstance()->getValueForKey('CHARSET') ? SettingsHelper::getInstance()->getValueForKey('CHARSET') : 'no', ['class' => 'form-select'] ) !!}

                                            @if ($errors->has('CHARSET'))
                                                <span class="text-danger">{{ $errors->first('CHARSET') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('CONTENT_TYPE', __('frontend.form.content_type'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            <!-- radio -->
                                            <div class="form-group">
                                                <div class="form-check">

                                                    {!! form_radio('CONTENT_TYPE', 'html', SettingsHelper::getInstance()->getValueForKey('CONTENT_TYPE') == 'html' || SettingsHelper::getInstance()->getValueForKey('CONTENT_TYPE') == '', ['class' => 'form-check-input'] ) !!}

                                                    <label class="form-check-label">HTML</label>
                                                </div>
                                                <div class="form-check">

                                                    {!! form_radio('CONTENT_TYPE', 'plain', SettingsHelper::getInstance()->getValueForKey('CONTENT_TYPE') == 'plain' ? true : false, ['class' => 'form-check-input'] ) !!}

                                                    <label class="form-check-label">Plain</label>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('HOW_TO_SEND', __('frontend.form.how_to_send'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            <!-- radio -->
                                            <div class="form-group">
                                                <div class="form-check">

                                                    {!! form_radio('HOW_TO_SEND', 'php', SettingsHelper::getInstance()->getValueForKey('HOW_TO_SEND') == 'php' || SettingsHelper::getInstance()->getValueForKey('HOW_TO_SEND') == '', ['class' => 'form-check-input'] ) !!}

                                                    <label class="form-check-label">PHP Mail</label>
                                                </div>
                                                <div class="form-check">

                                                    {!! form_radio('HOW_TO_SEND', 'smtp', SettingsHelper::getInstance()->getValueForKey('HOW_TO_SEND') == 'smtp' ? true : false, ['class' => 'form-check-input'] ) !!}

                                                    <label class="form-check-label">SMTP</label>
                                                </div>
                                                <div class="form-check">

                                                    {!! form_radio('HOW_TO_SEND', 'sendmail', SettingsHelper::getInstance()->getValueForKey('HOW_TO_SEND') == 'sendmail' ? true : false, ['class' => 'form-check-input'] ) !!}

                                                    <label class="form-check-label">Sendmail</label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('SENDMAIL_PATH', __('frontend.form.sendmail_path'), ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('SENDMAIL_PATH', SettingsHelper::getInstance()->getValueForKey('SENDMAIL_PATH'), ['placeholder' => __('frontend.form.sendmail_path'), 'class' => 'form-control']) !!}

                                            @if ($errors->has('SENDMAIL_PATH'))
                                                <span class="text-danger">{{ $errors->first('SENDMAIL_PATH') }}</span>
                                            @endif

                                        </div>

                                    </div>

                                    <div class="form-group row">

                                        {!! form_label('URL', 'URL', ['class' => 'col-sm-2 col-form-label']) !!}

                                        <div class="col-sm-10">

                                            {!! form_text('URL', SettingsHelper::getInstance()->getValueForKey('URL'), ['placeholder' => 'URL', 'class' => 'form-control']) !!}

                                            @if ($errors->has('URL'))
                                                <span class="text-danger">{{ $errors->first('URL') }}</span>
                                            @endif

                                        </div>

                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="s3">
                                    <div id="headerslist">

                                        @foreach($customHeaders ?? [] as $header)

                                            <div class="header-row">
                                                <div class="form-group row">

                                                    {!! form_label('header_name[]', __('frontend.form.name'), ['class' => 'col-sm-2 col-form-label']) !!}

                                                    <div class="col-md-3">

                                                        {!! form_text('header_name[]', $header->name, ['class' => 'form-control']) !!}

                                                    </div>

                                                    {!! form_label('header_value[]', __('frontend.form.value'), ['class' => 'col-sm-2 col-form-label']) !!}

                                                    <div class="col-md-3">

                                                        {!! form_text('header_value[]', $header->value, ['class' => 'form-control']) !!}

                                                    </div>

                                                    <div class="col-md-2">
                                                        <a class="btn btn-outline-danger btn-sm removeBlock" title="{{ __('frontend.form.remove') }}">
                                                            <i class="fas fa-minus"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                        @endforeach

                                        <div class="form-group mb-0">
                                            <div class="col-lg-12">
                                                <button class="btn btn-secondary btn-sm" id="add_field" type="button">
                                                    <i class="fas fa-plus me-1"></i>
                                                    {{ __('frontend.form.add') }}
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            {{ __('frontend.str.apply') }}
                        </button>
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
            $(document).on("click", '#add_field', function () {
                let html = '<div class="header-row"><div class="form-group row">';

                html += '<label class="col-sm-2 col-form-label">{{ __('frontend.str.name') }}</label>';
                html += '<div class="col-md-3"><input class="form-control" type="text" value="" name="header_name[]"></div>';
                html += '<label class="col-sm-2 col-form-label">{{ __('frontend.str.value') }}</label>';
                html += '<div class="col-md-3"><input class="form-control" type="text" value="" name="header_value[]"></div>';
                html += '<div class="col-md-2"><a class="btn btn-outline-danger btn-sm removeBlock" title="{{ __('frontend.form.remove') }}"><i class="fas fa-minus"></i></a></div>';
                html += '</div></div>';

                $('#headerslist').prepend(html);
                console.log(html);
            });

            $(document).on("click", '.removeBlock', function () {
                let parent = $(this).parents('div[class^="header-row"]').first();
                parent.remove();
            });
        });
    </script>

@endsection
