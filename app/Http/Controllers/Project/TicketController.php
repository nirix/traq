<?php
/*!
 * Traq
 *
 * Copyright (C) 2009-2019 Jack P.
 * Copyright (C) 2012-2019 Traq.io
 * https://github.com/nirix
 * https://traq.io
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq\Http\Controllers\Project;

use Traq\Events\TicketCreated;
use Traq\Events\TicketUpdated;
use Traq\Http\Controllers\Controller;
use Traq\Http\Requests\StoreTicketRequest;
use Traq\Http\Requests\UpdateTicketRequest;
use Traq\Milestone;
use Traq\Priority;
use Traq\Project;
use Traq\Status;
use Traq\Ticket;
use Traq\TicketUpdate;
use Traq\Type;
use Traq\User;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['index', 'show']
        ]);
    }

    /**
     * List tickets.
     *
     * @param Project $project
     */
    public function index(Project $project)
    {
        $tickets = $project->tickets()
            ->with(['milestone', 'type', 'status', 'priority'])
            ->paginate(50);

        return view('tickets/index', [
            'project' => $project,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Create ticket form.
     *
     * @param Project $project
     */
    public function create(Project $project)
    {
        $this->authorize('create', Ticket::class);

        $ticket = new Ticket();

        return view('tickets/create', [
            'project' => $project,
            'ticket' => $ticket,
        ]);
    }

    /**
     * Create ticket if validation passes.
     *
     * @param Project $project
     * @param StoreTicketRequest $request
     */
    public function store(Project $project, StoreTicketRequest $request)
    {
        $ticket = new Ticket([
            'summary' => $request->get('summary'),
            'description' => $request->get('description'),
            'milestone_id' => $request->get('milestone'),
            'type_id' => $request->get('type'),
            'version_id' => $request->get('version'),
            'status_id' => $project->defaultStatus->id,
            'priority_id' => $project->defaultPriority->id,
        ]);

        $ticket->ticket_id = $project->next_ticket_id;
        $ticket->project_id = $project->id;
        $ticket->user_id = auth()->id();

        $project->incrementTicketId();
        $project->save();
        $ticket->save();

        event(new TicketCreated($ticket));

        return redirect(route('tickets.show', [
            'project' => $project,
            'ticket' => $ticket->ticket_id
        ]));
    }

    /**
     * Show ticket.
     *
     * @param Project $project
     * @param integer $ticketId
     */
    public function show(Project $project, int $ticketId)
    {
        $ticket = $project->tickets->where('ticket_id', $ticketId)->first();

        return view('tickets/show', [
            'project' => $project,
            'ticket' => $ticket,
            'updates' => $ticket->updates()->with('user')->get()
        ]);
    }

    /**
     * Save ticket if validation passes.
     *
     * @param Project $project
     * @param Ticket $ticket
     * @param UpdateTicketRequest $request
     */
    public function update(
        Project $project,
        Ticket $ticket,
        UpdateTicketRequest $request
    ) {
        $changes = [];
        $action = Ticket::ACTION_UPDATED;
        $data = [];

        if ($ticket->summary !== $request->get('summary')) {
            $changes['summary'] = [
                'old' => $ticket->summary,
                'new' => $request->get('summary')
            ];

            $ticket->summary = $request->get('summary');
        }

        if ($ticket->status_id !== (int) $request->get('status')) {
            /** @var Status $newStatus */
            $newStatus = Status::find($request->get('status'));

            $changes['status'] = [
                'old' => $ticket->status->name,
                'new' => $newStatus->name
            ];

            if ($ticket->status->status !== $newStatus->status) {
                $data['status_name'] = $newStatus->name;

                if ($ticket->isClosed()) {
                    if ($newStatus->isOpen()) {
                        $action = Ticket::ACTION_REOPENED;
                        $ticket->is_closed = false;
                    }
                } else {
                    if ($newStatus->isClosed()) {
                        $action = Ticket::ACTION_CLOSED;
                        $ticket->is_closed = true;
                    }
                }
            }

            $ticket->status_id = $request->get('status');
        }

        if ($ticket->type_id !== (int) $request->get('type')) {
            $changes['type'] = [
                'old' => $ticket->type->name,
                'new' => Type::find($request->get('type'))->name
            ];

            $ticket->type_id = $request->get('type');
        }

        if ($ticket->priority_id !== (int) $request->get('priority')) {
            $changes['priority'] = [
                'old' => $ticket->priority->name,
                'new' => Priority::find($request->get('priority'))->name
            ];

            $ticket->priority_id = $request->get('priority');
        }

        if ($ticket->milestone_id !== (int) $request->get('milestone')) {
            $changes['milestone'] = [
                'old' => $ticket->milestone->name,
                'new' => Milestone::find($request->get('milestone'))->name
            ];

            $ticket->milestone_id = $request->get('milestone');
        }

        if ($ticket->version_id !== (int) $request->get('version')) {
            $changes['version'] = [
                'old' => $ticket->version ? $ticket->version->name : null,
                'new' => ($version = Milestone::find($request->get('version'))) ? $version->name : null,
            ];

            $ticket->version_id = $request->get('version');
        }

        if ($ticket->assignee_id !== $request->get('assignee')) {
            $changes['assignee'] = [
                'old' => $ticket->assignee ? $ticket->assignee->name : null,
                'new' => ($assignee = User::find($request->get('assignee'))) ? $assignee->name : null,
            ];

            $ticket->assignee_id = $request->get('assignee');
        }

        if (empty($changes) && empty($request->get('comment'))) {
            return redirect(route('tickets.show', [
                'project' => $project,
                'ticket' => $ticket,
            ]));
        }

        TicketUpdate::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->user()->id,
            'comment' => $request->get('comment'),
            'change_data' => $changes
        ]);

        $ticket->save();

        event(new TicketUpdated($ticket, $action, $data));

        return redirect(route('tickets.show', ['project' => $project, 'ticket' => $ticket]))
            ->with('success', __('tickets.updated_successfully'));
    }
}
