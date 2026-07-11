@extends('admin.app')

@section('title', $title)

@section('breadcrumbs')
    <ol class="breadcrumb float-sm-end">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard.index') }}">{{ __('frontend.str.admin_panel') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.projects.index') }}">{{ __('frontend.str.projects') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ $project->name }}</li>
    </ol>
@endsection

@section('content')

    <div class="container-fluid project-page">
        <div class="row g-3">
            <div class="col-12">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-folder-open me-1"></i>
                            {{ __('frontend.str.project') }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">ID</dt>
                            <dd class="col-sm-9">{{ $project->id }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.project') }}</dt>
                            <dd class="col-sm-9">{{ $project->name }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.organization') }}</dt>
                            <dd class="col-sm-9">{{ optional($project->organization)->name ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.status') }}</dt>
                            <dd class="col-sm-9">
                                <span class="badge {{ \App\Enums\ProjectStatus::badgeClassFor($project->status) }}">
                                    {{ $project->status_label }}
                                </span>
                            </dd>

                            <dt class="col-sm-3">{{ __('frontend.str.default_sender_name') }}</dt>
                            <dd class="col-sm-9">{{ $project->default_sender_name ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.default_from_email') }}</dt>
                            <dd class="col-sm-9">{{ $project->default_from_email ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.default_reply_to') }}</dt>
                            <dd class="col-sm-9">{{ $project->default_reply_to ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.timezone') }}</dt>
                            <dd class="col-sm-9">{{ $project->timezone ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.form.description') }}</dt>
                            <dd class="col-sm-9">{{ $project->description ?: '-' }}</dd>

                            <dt class="col-sm-3">{{ __('frontend.str.added') }}</dt>
                            <dd class="col-sm-9">{{ optional($project->created_at)->format('d.m.Y H:i') ?: '-' }}</dd>
                        </dl>
                    </div>

                    <div class="card-footer form-actions-footer d-flex flex-column flex-sm-row gap-2 justify-content-start">
                        <a class="btn btn-secondary btn-back" href="{{ route('admin.projects.index') }}">
                            {{ __('frontend.form.back') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-friends me-1"></i>
                            {{ __('frontend.menu.subscribers') }}
                        </h3>

                        <div class="card-tools">
                            <a href="{{ route('admin.projects.subscribers.create', ['project' => $project->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('frontend.str.add_subscriber') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.name') }}</th>
                                    <th>E-mail</th>
                                    <th>{{ __('frontend.str.category') }}</th>
                                    <th>{{ __('frontend.str.status') }}</th>
                                    <th>{{ __('frontend.str.added') }}</th>
                                    <th class="text-end">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($subscribers as $subscriber)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.subscribers.edit', ['id' => $subscriber->id]) }}">
                                                <strong>{{ $subscriber->name ?: '-' }}</strong>
                                            </a>
                                        </td>
                                        <td>{{ $subscriber->email }}</td>
                                        <td>
                                            @php
                                                $categories = $subscriber->subscriptions
                                                    ->map(fn ($subscription) => $subscription->category?->name)
                                                    ->filter()
                                                    ->unique();
                                            @endphp

                                            @forelse($categories as $category)
                                                <span class="badge text-bg-secondary me-1">{{ $category }}</span>
                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if((int) $subscriber->active === 1)
                                                <span class="badge text-bg-success">{{ __('frontend.str.yes') }}</span>
                                            @else
                                                <span class="badge text-bg-secondary">{{ __('frontend.str.no') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($subscriber->created_at)->format('d.m.Y H:i') ?: '-' }}</td>
                                        <td class="text-end">
                                            <div class="d-inline-flex align-items-center flex-nowrap gap-1">
                                                <a class="btn btn-outline-secondary btn-sm"
                                                   href="{{ route('admin.subscribers.edit', ['id' => $subscriber->id]) }}"
                                                   title="{{ __('frontend.str.show') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <form action="{{ route('admin.projects.subscribers.destroy', ['project' => $project->id, 'subscriber' => $subscriber->id]) }}"
                                                      method="post"
                                                      class="d-inline"
                                                      onsubmit="return confirm('{{ __('frontend.str.confirm_remove') }}');">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ __('frontend.str.remove_from_project') }}">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            {{ __('frontend.str.no_subscribers') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($subscribers->hasPages())
                        <div class="card-footer d-flex justify-content-center">
                            {{ $subscribers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
