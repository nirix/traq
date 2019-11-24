@extends('layouts.admin')

@section('title')
    {{ __('projects.projects') }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="float-right">
                    @can('create', Traq\Project::class)
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                        <i class="fas fa-fw fa-plus"></i>
                        <span class="d-none d-md-inline">{{ __('projects.new_project') }}</span>
                    </a>
                    @endcan
                </div>

                <h1 class="page-title">
                    {{ __('projects.projects') }}
                </h1>

                <div class="card border-top-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('projects.name') }}</th>
                                <th>{{ __('projects.codename') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>
                                    @can('update', $project)
                                    <a href="{{ route('projects.settings', ['project' => $project]) }}">
                                        {{ $project->name }}
                                    </a>
                                    @else
                                        {{ $project->name }}
                                    @endcan
                                </td>
                                <td>
                                    {{ $project->codename }}
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                    @can('update', $project)
                                        <a href="{{ route('projects.settings', ['project' => $project]) }}" class="btn btn-sm btn-warning" title="{{ __('forms.edit') }}">
                                            <i class="fas fa-fw fa-pencil-alt"></i>
                                            <span class="sr-only">{{ __('forms.edit') }}</span>
                                        </a>
                                    @endcan
                                    @can('delete', $project)
                                        <form action="{{ route('admin.projects.destroy', ['project' => $project->id]) }}" onsubmit="return confirm('{{ __('forms.confirm_delete_x', ['item' => $project->name]) }}')" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('forms.delete') }}">
                                                <i class="fas fa-fw fa-trash"></i>
                                                <span class="sr-only">{{ __('forms.delete') }}</span>
                                            </button>
                                        </form>
                                    @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
