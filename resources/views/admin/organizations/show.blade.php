@extends('admin.app')

@section('title', $title)

@section('content')

    @php
        $statusClasses = [
            \App\Models\Project::STATUS_ACTIVE => 'text-bg-success',
            \App\Models\Project::STATUS_ARCHIVED => 'text-bg-secondary',
            \App\Models\Project::STATUS_BLOCKED => 'text-bg-danger',
        ];
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building me-1"></i>
                            {{ $organization->name }}
                        </h3>

                        <div class="card-tools">
                            <a href="{{ route('admin.organizations.edit', ['organization' => $organization->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                {{ __('frontend.form.edit') }}
                            </a>
                            <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary btn-sm">
                                {{ __('frontend.form.back') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">{{ __('frontend.str.description') }}</dt>
                            <dd class="col-sm-9">{{ $organization->description ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.added') }}</dt>
                            <dd class="col-sm-9">{{ optional($organization->created_at)->format('d.m.Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-folder-open me-1"></i>
                            {{ __('frontend.str.projects') }}
                        </h3>

                        <div class="card-tools">
                            <a href="{{ route('admin.projects.create', ['organization' => $organization->id]) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('frontend.str.add_project') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.project') }}</th>
                                    <th>{{ __('frontend.str.status') }}</th>
                                    <th>{{ __('frontend.str.default_sender_name') }}</th>
                                    <th>{{ __('frontend.str.default_from_email') }}</th>
                                    <th>{{ __('frontend.str.default_reply_to') }}</th>
                                    <th>{{ __('frontend.str.timezone') }}</th>
                                    <th>{{ __('frontend.str.language') }}</th>
                                    <th>{{ __('frontend.str.unsubscribe_template') }}</th>
                                    <th>{{ __('frontend.str.added') }}</th>
                                    <th class="text-end">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($organization->projects as $project)
                                    <tr>
                                        <td>
                                            <strong>{{ $project->name }}</strong>
                                            @if($project->description)
                                                <div class="small text-muted">{{ $project->description }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusClasses[$project->status] ?? 'text-bg-secondary' }}">
                                                {{ $project->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $project->default_sender_name ?: '-' }}</td>
                                        <td>{{ $project->default_from_email ?: '-' }}</td>
                                        <td>{{ $project->default_reply_to ?: '-' }}</td>
                                        <td>{{ $project->timezone ?: '-' }}</td>
                                        <td>{{ $project->locale ? (config('app.languages')[$project->locale] ?? $project->locale) : '-' }}</td>
                                        <td>{{ optional($project->unsubscribeTemplate)->name ?: '-' }}</td>
                                        <td>{{ optional($project->created_at)->format('d.m.Y H:i') }}</td>
                                        <td class="text-end">
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.projects.edit', ['organization' => $organization->id, 'project' => $project->id]) }}" title="{{ __('frontend.form.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.projects.destroy', ['organization' => $organization->id, 'project' => $project->id]) }}"
                                                  method="post"
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('frontend.str.confirm_remove') }}');">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('frontend.str.remove') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            {{ __('frontend.str.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
