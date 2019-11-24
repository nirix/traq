@extends('layouts.project')

@section('title')
    {{ $ticket->summary }} (#{{ $ticket->ticket_id }}) - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="card bg-ticket ticket mb-4  ">
            <div class="card-body">
                <h1 class="page-title">
                    {{ $ticket->summary }} <small><em>(#{{ $ticket->ticket_id }})</em></small>
                </h1>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-md-5">
                            <div class="ticket-property-label d-md-inline-block mr-md-2">
                                {{ __('tickets.type') }}
                            </div>
                            <div class="ticket-property-value d-md-inline-block text-md-right">
                                {{ $ticket->type->name }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="ticket-property-label d-md-inline-block mr-md-2">
                                {{ __('tickets.status') }}
                            </div>
                            <div class="ticket-property-value d-md-inline-block text-md-right">
                                {{ $ticket->status->name }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="ticket-property-label d-md-inline-block mr-md-2">
                                {{ __('tickets.milestone') }}
                            </div>
                            <div class="ticket-property-value d-md-inline-block text-md-right">
                                {{ $ticket->milestone->name }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="ticket-property-label d-md-inline-block mr-md-2">
                                {{ __('tickets.version') }}
                            </div>
                            <div class="ticket-property-value d-md-inline-block text-md-right">
                                {{ $ticket->version ? $ticket->version->name : '-' }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="ticket-property-label d-md-inline-block mr-md-2">
                                {{ __('tickets.priority') }}
                            </div>
                            <div class="ticket-property-value d-md-inline-block text-md-right">
                                {{ $ticket->priority->name }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="ticket-property-label d-md-inline-block mr-md-2">
                                {{ __('tickets.reported_by') }}
                            </div>
                            <div class="ticket-property-value d-md-inline-block text-md-right">
                                {{ $ticket->user->name }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="ticket-property-label d-md-inline-block mr-md-2">
                                {{ __('tickets.created') }}
                            </div>
                            <div class="ticket-property-value d-md-inline-block text-md-right">
                                {{ $ticket->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <div class="ticket-property-label d-md-inline-block mr-md-2">
                                {{ __('tickets.updated') }}
                            </div>
                            <div class="ticket-property-value d-md-inline-block text-md-right">
                                {{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ticket-description">
                    <div>
                        @parsedown($ticket->description)
                    </div>
                </div>
            </div>
        </div>

        @include('tickets/_update_form', [
            'project' => $project,
            'ticket' => $ticket,
        ])

        <section>
            <h2 class="section-title mb-0">{{ __('ticket_updates.ticket_history') }}</h2>
            @forelse($updates as $update)
            <div class="ticket-update card mb-2">
                <div class="card-body">
                    <div class="card-title">{{ __('ticket_updates.update_title', ['user_name' => $update->user->name, 'ago' => $update->created_at->diffForHumans()]) }}</div>
                    @if($update->comment)
                    <div class="ticket-comment card-text">
                        @parsedown($update->comment)
                    </div>
                    @endif
                    @if($update->change_data)
                        <div class="ticket-changes container-fluid">
                            <div class="row">
                                @foreach($update->changeDataWithInfo() as $change)
                                    <div class="col-md-4">
                                        <div class="ticket-update-change-label d-md-inline-block mr-md-2">
                                            {{ $change['label'] }}
                                        </div>
                                        <div class="ticket-update-change-value d-md-inline-block text-md-right">
                                            @if($change['old'])
                                                <span class="ticket-update-change-from">{{ $change['old'] }}</span>
                                            @endif
                                            @if($change['new'])
                                                <span class="ticket-update-change-to">{{ $change['new'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @empty
                <div class="text-center">{{ __('ticket_updates.no_updates') }}</div>
            @endforelse
        </section>
    </div>
@endsection
