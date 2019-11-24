@extends('layouts.app')

@section('navbar_brand', $project->name)
@section('navbar_brand_url', route('project.show', ['project' => $project]))

@section('title')
    {{ $project->name }}
@endsection

@section('navbar_right')
    @can('manage', $project)
    <li class="nav-item dropdown">
        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre title="{{ __('projects.project_settings') }}">
            <i class="fas fa-fw fa-cog"></i> <span class="caret"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            @can('update', $project)
                <a class="dropdown-item" href="{{ route('projects.settings', ['project' => $project]) }}">
                    {{ __('projects.settings') }}
                </a>
            @endcan
        </div>
    </li>
    @endcan
@endsection

@section('navbar')
    <li class="nav-item">
        <a href="{{ route('project.show', ['project' => $project]) }}" class="nav-link @if(Request::routeIs('project.show')) active @endif">
            {{ __('projects.info') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('project.timeline', ['project' => $project]) }}" class="nav-link @if(Request::routeIs('project.timeline')) active @endif">
            {{ __('projects.timeline') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('project.roadmap', ['project' => $project]) }}" class="nav-link @if(Request::routeIs('project.roadmap')) active @endif">
            {{ __('projects.roadmap') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('tickets.index', ['project' => $project]) }}" class="nav-link @if(Request::routeIs('tickets.index')) active @endif">
            {{ __('projects.tickets') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('project.changelog', ['project' => $project]) }}" class="nav-link @if(Request::routeIs('project.changelog')) active @endif">
            {{ __('projects.changelog') }}
        </a>
    </li>
    @if($project->enable_wiki)
        @can('view', \Traq\WikiPage::class)
            <li class="nav-item">
                <a href="{{ route('wiki.index', ['project' => $project]) }}" class="nav-link @if(Request::routeIs('wiki.index')) active @endif">
                    {{ __('projects.wiki') }}
                </a>
            </li>
        @endcan
    @endif
@endsection
