@extends('layouts.project')

@section('title')
    {{__('projects.changelog') }} - @parent
@endsection

@section('content')
    <div class="container">
        <h1 class="page-title">
            {{ __('projects.changelog') }}
        </h1>
        <div class="card">
            <div class="card-body">
                @forelse($milestones as $milestone)
                <div class="changelog-milestone">
                    <h2 class="milestone-name border-bottom">{{ $milestone->name }}</h2>
                    <ul>
                        @forelse($milestone->tickets as $ticket)
                            <li>
                                <a href="{{ route('tickets.show', ['project' => $project, 'ticket' => $ticket]) }}">
                                    {{ $ticket->summary }}
                                </a>
                            </li>
                        @empty
                            <li>{{ __('projects.no_changes') }}</li>
                        @endforelse
                    </ul>
                </div>
                @empty
                    <div class="text-center">
                        {{ __('projects.no_changes') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
