@extends('layouts.admin')

@section('title')
    {{ __('users.users') }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="float-right">
                    @can('create', Traq\UserGroup::class)
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-fw fa-plus"></i>
                            <span class="d-none d-md-inline">{{ __('users.new_user') }}</span>
                        </a>
                    @endcan
                </div>

                <h1 class="page-title">
                    {{ __('users.users') }}
                </h1>

                <div class="card border-top-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('users.name') }}</th>
                            <th>{{ __('users.user_group') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $usr)
                            <tr>
                                <td>
                                    @can('update', $usr)
                                        <a href="{{ route('admin.users.edit', ['user' => $usr]) }}">
                                            {{ $usr->name }}
                                        </a>
                                    @else
                                        {{ $usr->name }}
                                    @endcan
                                </td>
                                <td>
                                    {{ $usr->group->name }}
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        @can('update', $usr)
                                            <a href="{{ route('admin.users.edit', ['user' => $usr]) }}" class="btn btn-sm btn-warning" title="{{ __('forms.edit') }}">
                                                <i class="fas fa-fw fa-pencil-alt"></i>
                                                <span class="sr-only">{{ __('forms.edit') }}</span>
                                            </a>
                                        @endcan
                                        @can('delete', $usr)
                                            <a href="{{ route('admin.users.destroy', ['user' => $usr->id]) }}" class="btn btn-sm btn-danger" title="{{ __('forms.delete') }}">
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
