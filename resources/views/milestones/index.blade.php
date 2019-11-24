@extends('layouts.project')

@section('title')
    {{ __('projects.roadmap') }} - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="float-right">
                    @can('create', Traq\Milestone::class)
                    <a href="{{ route('milestones.create', ['project' => $project]) }}" class="btn btn-primary" title="{{ __('milestones.new_milestone') }}">
                        <i class="fas fa-fw fa-plus"></i>
                        <span class="d-none d-md-inline">{{ __('milestones.new_milestone') }}</span>
                    </a>
                    @endcan
                </div>

                <h1 class="page-title">
                    {{ __('projects.roadmap') }}
                </h1>

                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link @if($filter === 'active') active @endif" href="{{ route('project.roadmap', ['project' => $project]) }}">
                                    {{ __('milestones.active') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($filter === 'completed') active @endif" href="{{ route('project.roadmap', ['project' => $project, 'filter' => 'completed']) }}">
                                    {{ __('milestones.completed') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($filter === 'all') active @endif" href="{{ route('project.roadmap', ['project' => $project, 'filter' => 'all']) }}">
                                    {{ __('milestones.all') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        @foreach ($milestones as $milestone)
                            <section class="milestone">
                                <h3 class="milestone-name">
                                    <a href="{{ route('milestones.show', ['project' => $project->slug, 'milestone' => $milestone->slug]) }}">
                                        {{ $milestone->name }} @if ($milestone->codename)<em>({{ $milestone->codename }})</em>@endif
                                    </a>
                                </h3>
                                @if ($milestone->due_at)
                                    <div class="text-muted mb-2">
                                        {{ __('milestones.due_x', ['due' => $milestone->due_at->diffForHumans()]) }}
                                    </div>
                                @endif
                                @include('milestones/_progress_bar', ['milestone' => $milestone])
                                <div>
                                    {{ $milestone->description }}
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
