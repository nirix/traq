@extends('layouts.project')

@section('title')
    {{ __('projects.tickets') }} - {{ $project->name }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="float-right">
                    @can('create', Traq\Ticket::class)
                    <a href="{{ route('tickets.create', ['project' => $project]) }}" class="btn btn-primary" title="{{ __('tickets.new_ticket') }}">
                        <i class="fas fa-fw fa-plus"></i>
                        <span class="d-none d-md-inline">{{ __('tickets.new_ticket') }}</span>
                    </a>
                    @endcan
                </div>

                <h1 class="page-title">
                    {{ __('projects.tickets') }}
                </h1>

                <div class="card border-top-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('tickets.summary') }}</th>
                                <th>{{ __('tickets.type') }}</th>
                                <th>{{ __('tickets.status') }}</th>
                                <th>{{ __('tickets.milestone') }}</th>
                                <th>{{ __('tickets.priority') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($tickets as $ticket)
                            <tr>
                                <td>
                                    <a href="{{ route('tickets.show', ['project' => $project, 'ticket' => $ticket->ticket_id]) }}">
                                        {{ $ticket->ticket_id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('tickets.show', ['project' => $project, 'ticket' => $ticket->ticket_id]) }}">
                                        {{ $ticket->summary }}
                                    </a>
                                </td>
                                <td>
                                    {{ $ticket->type->name }}
                                </td>
                                <td>
                                    {{ $ticket->status->name }}
                                </td>
                                <td>
                                    {{ $ticket->milestone->name }}
                                </td>
                                <td>
                                    {{ $ticket->priority->name }}
                                </td>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="10">
                                    {{ __('tickets.no_tickets') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-2">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
