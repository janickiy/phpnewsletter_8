@extends('admin.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div class="container-fluid faq-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-question-circle me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    <div class="card-body">
                        {!! __('faq.str') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

@endsection
