@extends('layouts.admin')

@section('title')
    {{ __('usersgroups.user_groups') }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="float-right">
                    @can('create', Traq\UserGroup::class)
                        <a href="{{ route('admin.user-groups.create') }}" class="btn btn-primary">
                            <i class="fas fa-fw fa-plus"></i>
                            <span class="d-none d-md-inline">{{ __('usergroups.new_user_group') }}</span>
                        </a>
                    @endcan
                </div>

                <h1 class="page-title">
                    {{ __('usergroups.user_groups') }}
                </h1>

                <div class="card border-top-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('usergroups.name') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($userGroups as $userGroup)
                            <tr>
                                <td>
                                    @can('update', $userGroup)
                                        <a href="{{ route('admin.user-groups.edit', ['user_group' => $userGroup]) }}">
                                            {{ $userGroup->name }}
                                        </a>
                                    @else
                                        {{ $userGroup->name }}
                                    @endcan
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        @can('update', $userGroup)
                                            <a href="{{ route('admin.user-groups.edit', ['user_group' => $userGroup]) }}" class="btn btn-sm btn-warning" title="{{ __('forms.edit') }}">
                                                <i class="fas fa-fw fa-pencil-alt"></i>
                                                <span class="sr-only">{{ __('forms.edit') }}</span>
                                            </a>
                                        @endcan
                                        @can('delete', $userGroup)
                                            <a href="{{ route('admin.user-groups.destroy', ['user_group' => $userGroup->id]) }}" class="btn btn-sm btn-danger" title="{{ __('forms.delete') }}">
                                                <i class="fas fa-fw fa-trash"></i>
                                                <span class="sr-only">{{ __('forms.delete') }}</span>
                                            </a>
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
