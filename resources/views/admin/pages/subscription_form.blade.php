@extends('admin.app')

@section('title', $title)

@section('css')

    <link rel="stylesheet" href="{{ asset('plugins/highlightjs/styles/github-dark.css') }}">

    <style>

        .subscription-form-page pre {
            position: relative;
            border: 1px solid #30363d !important;
            border-radius: 8px;
            background: #0d1117 !important;
            padding: 0 !important;
            margin-bottom: 0 !important;
            font-size: 14px !important;
            overflow: auto;
        }

        .subscription-form-page pre code {
            background: #0d1117 !important;
            font-size: 13.5px !important;
            white-space: pre;
        }

        .subscription-form-page .hljs {
            background: #0d1117 !important;
        }

        .subscription-form-page .hljs-ln {
            width: 100%;
        }

        .subscription-form-page .hljs-ln td {
            padding: 0;
        }

        .subscription-form-page .hljs-ln-numbers {
            background: #010409;
            border-right: 1px solid #30363d;
            color: #6e7681;
            min-width: 42px;
            padding-right: 12px !important;
            text-align: right;
            user-select: none;
            vertical-align: top;
        }

        .subscription-form-page .hljs-ln-code {
            padding-left: 14px !important;
        }

        .subscription-preview {
            max-width: 720px;
        }

        .subscription-code-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            justify-content: flex-end;
        }

        @media (max-width: 575.98px) {
            .subscription-code-actions {
                justify-content: flex-start;
                margin-top: .75rem;
                width: 100%;
            }
        }

    </style>

@endsection


@section('content')

    <div class="container-fluid subscription-form-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-envelope-open-text me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="subscription-preview">
                            @include('include.subform')
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-code me-1"></i>
                            HTML
                        </h3>

                        <div class="card-tools subscription-code-actions">
                            <button type="button" class="btn btn-outline-primary btn-sm copy-code-button"
                                    onclick="copyToClipboard('#codebox')">
                                <i class="fas fa-copy me-1"></i>
                                {{ __('frontend.str.copy_to_clipboard') }}
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <pre><code class="language-html" id="codebox">{{ $embedCode }}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script src="{{ asset('plugins/highlightjs/highlight.js') }}"></script>
    <script src="{{ asset('plugins/highlightjs/highlightjs-line-numbers.js') }}"></script>

    <script>hljs.highlightAll();</script>
    <script>hljs.initLineNumbersOnLoad();</script>

    <script>
        async function copyToClipboard(element) {
            const content = $(element).text().trim();

            if (navigator.clipboard) {
                await navigator.clipboard.writeText(content);
                return;
            }

            let $temp = $("<textarea>");
            $("body").append($temp);
            $temp.val(content).select();
            document.execCommand("copy");
            $temp.remove();
        }
    </script>

@endsection
