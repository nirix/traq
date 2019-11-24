@extends('layouts.project')

@section('title')
    {{__('projects.timeline') }} - @parent
@endsection

@section('content')
    <div class="container">
        <h1 class="page-title">
            {{ __('projects.timeline') }}
        </h1>
        <div class="card">
            <div class="card-body">
                @forelse($groupedEvents as $group)
                    <h2 class="border-bottom">{{ $group['date']->format('F jS, Y') }}</h2>
                    @foreach($group['events'] as $userEvents)
                        <div class="timeline-user-set">
                            <ul class="timeline-user-set-events">
                                @foreach($userEvents['events'] as $event)
                                    <li>
                                        <span>{{ $event->created_at->format('h:ia') }}</span>
                                        -
                                        <span>
                                            @if($event->objectTypeIs(Traq\Ticket::class))
                                                <a href="{{ route('tickets.show', ['project' => $project, 'ticket' => $event->data['ticket_id']]) }}">
                                                    {{ $event->getReadableSummary() }}
                                                </a>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="timeline-user-set-author">
                                by <a href="{{ route('users.profile', ['user' => $userEvents['user']]) }}">{{ $userEvents['user']->name }}</a>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="text-center">
                        {{ __('timeline.no_events') }}
                    </div>
                @endforelse

                {{ $events->links() }}
            </div>
        </div>
    </div>
@endsection
