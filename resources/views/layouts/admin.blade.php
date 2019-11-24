@extends('layouts.app')

@section('navbar_brand', __('admin.admincp'))
@section('navbar_brand_url', route('admin.settings'))

@section('title')
    {{ __('admin.admincp') }}
@endsection

@section('navbar_right')
@endsection

@section('navbar')
    <li class="nav-item">
        <a href="{{ route('admin.settings') }}" class="nav-link @if(Request::routeIs('admin.settings')) active @endif">
            {{ __('admin.settings') }}
        </a>
    </li>
    @can('manage', Traq\Project::class)
    <li class="nav-item">
        <a href="{{ route('admin.projects.index') }}" class="nav-link @if(Request::routeIs('admin.projects.index')) active @endif">
            {{ __('admin.projects') }}
        </a>
    </li>
    @endcan
    @can('manage', Traq\User::class)
    <li class="nav-item">
        <a href="{{ route('admin.users.index') }}" class="nav-link @if(Request::routeIs('admin.users.index')) active @endif">
            {{ __('admin.users') }}
        </a>
    </li>
    @endcan
    @can('manage', Traq\UserGroup::class)
    <li class="nav-item">
        <a href="{{ route('admin.user-groups.index') }}" class="nav-link @if(Request::routeIs('admin.user-groups.index')) active @endif">
            {{ __('admin.groups') }}
        </a>
    </li>
    @endcan
    @can('manage', Traq\Type::class)
    <li class="nav-item">
        <a href="{{ route('admin.types.index') }}" class="nav-link @if(Request::routeIs('admin.types.index')) active @endif">
            {{ __('admin.types') }}
        </a>
    </li>
    @endcan
    @can('manage', Traq\Status::class)
    <li class="nav-item">
        <a href="{{ route('admin.statuses.index') }}" class="nav-link @if(Request::routeIs('admin.statuses.index')) active @endif">
            {{ __('admin.statuses') }}
        </a>
    </li>
    @endcan
@endsection
