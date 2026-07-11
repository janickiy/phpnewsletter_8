@extends('admin.app')

@section('title', $title)

@section('breadcrumbs')
    <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard.index') }}">{{ __('frontend.str.admin_panel') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ __('frontend.str.projects') }}</li>
    </ol>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-folder-open me-1"></i>
                            {{ __('frontend.str.projects') }}
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.project') }}</th>
                                    <th>{{ __('frontend.str.organization') }}</th>
                                    <th>{{ __('frontend.str.status') }}</th>
                                    <th>{{ __('frontend.str.default_sender_name') }}</th>
                                    <th>{{ __('frontend.str.default_from_email') }}</th>
                                    <th class="text-center">{{ __('frontend.menu.templates') }}</th>
                                    <th class="text-center">{{ __('frontend.menu.subscribers') }}</th>
                                    <th>{{ __('frontend.str.added') }}</th>
                                    <th class="text-end">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($projects as $project)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.projects.moderator.show', ['project' => $project->id]) }}">
                                                <strong>{{ $project->name }}</strong>
                                            </a>
                                            @if($project->description)
                                                <div class="small text-muted">{{ $project->description }}</div>
                                            @endif
                                        </td>
                                        <td>{{ optional($project->organization)->name ?: '-' }}</td>
                                        <td>
                                            <span class="badge {{ \App\Enums\ProjectStatus::badgeClassFor($project->status) }}">
                                                {{ $project->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $project->default_sender_name ?: '-' }}</td>
                                        <td>{{ $project->default_from_email ?: '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge text-bg-primary">{{ $project->templates_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge text-bg-success">{{ $project->subscribers_count }}</span>
                                        </td>
                                        <td>{{ optional($project->created_at)->format('d.m.Y H:i') }}</td>
                                        <td class="text-end">
                                            <a class="btn btn-outline-secondary btn-sm"
                                               href="{{ route('admin.projects.moderator.show', ['project' => $project->id]) }}"
                                               title="{{ __('frontend.str.show') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            {{ __('frontend.str.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($projects->hasPages())
                        <div class="card-footer d-flex justify-content-center">
                            {{ $projects->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
