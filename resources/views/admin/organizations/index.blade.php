@extends('admin.app')

@section('title', $title)

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building me-1"></i>
                            {{ __('frontend.menu.organizations') }}
                        </h3>

                        <div class="card-tools">
                            <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('frontend.str.add_organization') }}
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead>
                                <tr>
                                    <th>{{ __('frontend.str.name') }}</th>
                                    <th>{{ __('frontend.str.owner') }}</th>
                                    <th>{{ __('frontend.str.description') }}</th>
                                    <th class="text-center">{{ __('frontend.str.projects_number') }}</th>
                                    <th>{{ __('frontend.str.added') }}</th>
                                    <th class="text-end">{{ __('frontend.str.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($organizations as $organization)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.organizations.show', ['organization' => $organization->id]) }}">
                                                {{ $organization->name }}
                                            </a>
                                        </td>
                                        <td>{{ optional($organization->owner)->name ?: optional($organization->owner)->login ?: '-' }}</td>
                                        <td>{{ $organization->description ?: '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge text-bg-primary">{{ $organization->projects_count }}</span>
                                        </td>
                                        <td>{{ optional($organization->created_at)->format('d.m.Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a class="btn btn-outline-secondary" href="{{ route('admin.organizations.show', ['organization' => $organization->id]) }}" title="{{ __('frontend.str.show') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-primary" href="{{ route('admin.organizations.edit', ['organization' => $organization->id]) }}" title="{{ __('frontend.form.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>

                                            <form action="{{ route('admin.organizations.destroy', ['organization' => $organization->id]) }}"
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
                                        <td colspan="6" class="text-center text-muted py-4">
                                            {{ __('frontend.str.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($organizations->hasPages())
                        <div class="card-footer">
                            {{ $organizations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
