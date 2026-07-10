@extends('admin.app')

@section('title', $title)

@section('css')

@endsection

@section('content')

    <div class="container-fluid cron-job-page">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-terminal me-1"></i>
                            {{ $title }}
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="itemList" class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th>Cronjob</th>
                                    <th>{{ __('frontend.str.description') }}</th>
                                </tr>
                                </thead>

                                <tbody>

                                @foreach($cronJob as $job)
                                    <tr>
                                        <td>{{ $job['cron'] }}</td>
                                        <td>{{ $job['description'] }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

@endsection
