<?php
/*!
 * Traq
 * Copyright (C) 2009-2026 Jack Polgar
 * Copyright (C) 2012-2026 Traq.io
 * https://github.com/nirix
 * http://traq.io
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq\Helpers;

use avalon\helpers\Time;
use Traq\Models\Attachment;
use Traq\Models\Component;
use Traq\Models\CustomField;
use Traq\Models\CustomFieldValue;
use Traq\Models\Milestone;
use Traq\Models\Priority;
use Traq\Models\Project;
use Traq\Models\Severity;
use Traq\Models\Status;
use Traq\Models\Ticket;
use Traq\Models\TicketHistory;
use Traq\Models\Timeline;
use Traq\Models\Type;
use Traq\Models\User;
use Traq\Models\UserRole;

/**
 * Creates and updates tickets along with the records that go with them:
 * history, timeline events, attachments, custom field values and notifications.
 *
 * @author Jack P.
 * @package Traq
 * @subpackage Helpers
 * @since 3.9.0
 */
class TicketManager
{
    /**
     * Fields that reference another record, as field => [accessor, model].
     */
    private const RELATIONS = [
        'project_id'   => ['project', Project::class],
        'milestone_id' => ['milestone', Milestone::class],
        'version_id'   => ['version', Milestone::class],
        'component_id' => ['component', Component::class],
        'type_id'      => ['type', Type::class],
        'status_id'    => ['status', Status::class],
        'priority_id'  => ['priority', Priority::class],
        'severity_id'  => ['severity', Severity::class],
    ];

    /**
     * Reference fields that can be cleared.
     */
    public const NULLABLE = ['milestone_id', 'version_id', 'component_id', 'assigned_to_id'];

    /**
     * Reference fields that must belong to the ticket's project.
     */
    private const PROJECT_SCOPED = ['milestone_id', 'version_id', 'component_id'];

    /**
     * Fields stored as they are given.
     */
    private const SCALARS = ['summary', 'body', 'is_private', 'time_proposed', 'time_worked'];

    /**
     * Validated custom field values, as field id => [field, value].
     *
     * @var array<int, array{0: CustomField, 1: mixed}>
     */
    private array $customFields = [];

    /**
     * Records to save once the ticket itself has been saved.
     *
     * @var \Avalon\Database\Model[]
     */
    private array $queue = [];

    public function __construct(private Ticket $ticket, private User $user) {}

    /**
     * Returns the uploaded attachment, or null if there isn't one.
     * Adds an error to the ticket if the upload failed.
     */
    private function uploadedAttachment(): ?array
    {
        $file = $_FILES['attachment'] ?? null;

        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (
            $file['error'] !== UPLOAD_ERR_OK
            || !is_string($file['name'])
            || !is_string($file['tmp_name'])
            || !is_uploaded_file($file['tmp_name'])
        ) {
            $this->ticket->_add_error('attachment', l('errors.attachments.unable_to_upload_file'));

            return null;
        }

        return $file;
    }

    /**
     * Validates the submitted custom field values, adding errors to the ticket,
     * and keeps the valid ones to be saved with the ticket.
     *
     * @param CustomField[] $fields    Custom fields for the ticket's project.
     * @param array         $submitted Submitted values by custom field id.
     */
    public function setCustomFields(array $fields, array $submitted): void
    {
        foreach ($fields as $field) {
            $typeIds = $field->ticket_type_ids;

            if (!in_array(0, $typeIds) && !in_array($this->ticket->type_id, $typeIds)) {
                continue;
            }

            $value = $submitted[$field->id] ?? null;

            if (isset($value)) {
                if ($field->validate($value)) {
                    $this->customFields[$field->id] = [$field, $value];
                } else {
                    $this->ticket->_add_error($field->id, l('errors.custom_fields.x_is_not_valid', $field->name, $field->type));
                }
            }

            if ($field->is_required && empty($value)) {
                $this->ticket->_add_error($field->id, l('errors.custom_fields.x_required', $field->name));
            }
        }
    }

    /**
     * Updates the ticket with the values submitted in the request.
     *
     * @param CustomField[] $customFields Custom fields for the ticket's project.
     */
    public function updateFromRequest(TicketInput $input, array $customFields): bool
    {
        $data = $input->changes();

        if ($this->user->permission($this->ticket->project_id, 'add_attachments') && ($file = $this->uploadedAttachment())) {
            $data['attachment'] = $file;
        }

        $this->setCustomFields($customFields, $input->customFields());

        return $this->update($data, $input->comment());
    }

    /**
     * Saves a new ticket.
     */
    public function create(): bool
    {
        $ticket = $this->ticket;
        $project = $ticket->project;
        $status = Status::find($ticket->status_id ?: 1);

        $ticket->ticket_id = $project->next_tid;

        foreach (array_keys(self::RELATIONS) as $field) {
            if ($field !== 'project_id') {
                $this->resolve($field, (int) $ticket->{$field} ?: null, (int) $ticket->project_id);
            }
        }
        $this->resolve('assigned_to_id', (int) $ticket->assigned_to_id ?: null, (int) $ticket->project_id);

        if (!$ticket->save()) {
            return false;
        }

        $project->next_tid++;
        $project->save();

        foreach ($this->customFields as [$field, $value]) {
            (new CustomFieldValue([
                'custom_field_id' => $field->id,
                'ticket_id' => $ticket->id,
                'value' => $value
            ]))->save();
        }
        $this->customFields = [];

        (new Timeline([
            'project_id' => $ticket->project_id,
            'owner_id'   => $ticket->id,
            'action'     => 'ticket_created',
            'data'       => $status ? $status->name : 'unknown',
            'user_id'    => $ticket->user_id
        ]))->save();

        (new TicketHistory([
            'user_id' => $ticket->user_id,
            'ticket_id' => $ticket->id,
            'changes' => json_encode([
                [
                    'property' => 'status',
                    'action' => 'create',
                    'from' => null,
                    'to' => $status ? $status->name : null,
                ]
            ]),
            'comment' => ''
        ]))->save();

        // Created with a closed status.
        if ($ticket->is_closed) {
            (new Timeline([
                'project_id' => $ticket->project_id,
                'owner_id'   => $ticket->id,
                'action'     => 'ticket_closed',
                'data'       => $ticket->status_id,
                'user_id'    => $ticket->user_id,
                'created_at' => Time::date("Y-m-d H:i:s", time() - date("Z") + 1)
            ]))->save();
        }

        Notification::send_for_ticket('created', $ticket);

        if ($ticket->assigned_to_id) {
            Notification::send_to($ticket->assigned_to_id, 'ticket_assigned', ['ticket' => $ticket, 'project' => $project]);
        }

        return true;
    }

    /**
     * Updates the ticket properties, recording the changes in the ticket history
     * and timeline, then notifying subscribers.
     *
     * The `attachment` field takes an uploaded file from `$_FILES`.
     *
     * @param array       $data    Ticket field => new value.
     * @param string|null $comment Comment to add to the history entry.
     */
    public function update(array $data, ?string $comment = null): bool
    {
        $ticket = $this->ticket;
        $projectId = (int) ($data['project_id'] ?? $ticket->project_id);

        $changes = [];
        $statusEvent = $closing = $reopening = $reassigned = false;

        foreach ($data as $field => $value) {
            if ($field === 'attachment') {
                if (is_array($value) && ($change = $this->queueAttachment($value))) {
                    $changes[] = $change;
                }

                continue;
            }

            $isRelation = isset(self::RELATIONS[$field]);

            if (!$isRelation && !in_array($field, [...self::SCALARS, 'assigned_to_id', 'tasks'], true)) {
                throw new \InvalidArgumentException("Unknown ticket field: {$field}");
            }

            if ($isRelation || $field === 'assigned_to_id') {
                $value = (int) $value ?: null;
            }

            if ($ticket->{$field} == $value) {
                continue;
            }

            $target = null;

            if ($isRelation || $field === 'assigned_to_id') {
                $target = $this->resolve($field, $value, $projectId);

                if ($target === false) {
                    continue;
                }
            }

            if ($isRelation) {
                $current = $ticket->{self::RELATIONS[$field][0]};
                $from = $current ? $current->name : null;
                $to = $target ? $target->name : null;
            } elseif ($field === 'assigned_to_id') {
                $from = $ticket->assigned_to_id ?: null;
                $to = $value;
            } elseif ($field === 'tasks') {
                if (!is_array($value)) {
                    $ticket->_add_error('tasks', l('errors.invalid_value'));

                    continue;
                }

                // Only the task labels are recorded.
                $from = array_column($ticket->tasks ?: [], 'task');
                $to = array_column($value, 'task');
            } else {
                $from = $ticket->{$field};
                $to = $value;
            }

            if ($from == $to) {
                continue;
            }

            $change = [
                'property' => str_replace('_id', '', $field),
                'from' => $from,
                'to' => $to
            ];

            if ($field === 'status_id' && (int) $ticket->status->status !== (int) $target->status) {
                [$action, $timelineAction] = static::statusTransition($ticket->status, $target);

                $this->queue[] = new Timeline([
                    'project_id' => $ticket->project_id,
                    'owner_id' => $ticket->id,
                    'action' => $timelineAction,
                    'data' => $target->name,
                    'user_id' => $this->user->id
                ]);

                if ($action) {
                    $change['action'] = $action;
                }

                $statusEvent = true;
                $closing = $action === 'close';
                $reopening = $action === 'reopen';
            }

            $reassigned = $reassigned || $field === 'assigned_to_id';

            $ticket->set($field, $value);
            $changes[] = $change;
        }

        $changes = array_merge($changes, $this->updateCustomFields());

        if (count($changes) > 0 || !empty($comment)) {
            $this->queue[] = new TicketHistory([
                'user_id' => $this->user->id,
                'ticket_id' => $ticket->id,
                'changes' => count($changes) > 0 ? json_encode($changes) : '',
                'comment' => $comment ?? ''
            ]);

            // Status changes and moves add their own timeline events.
            if (!$statusEvent && !isset($data['project_id'])) {
                $timeline = [
                    'project_id' => $ticket->project_id,
                    'owner_id' => $ticket->id,
                    'action' => count($changes) > 0 ? 'ticket_updated' : 'ticket_comment',
                    'user_id' => $this->user->id
                ];

                if (count($changes) > 0) {
                    $timeline['data'] = $ticket->id;
                }

                $this->queue[] = new Timeline($timeline);
            }
        }

        if (!$ticket->save()) {
            return false;
        }

        foreach ($this->queue as $record) {
            $record->save();
        }
        $this->queue = [];

        Notification::send_for_ticket($closing ? 'closed' : ($reopening ? 'reopened' : 'updated'), $ticket);

        if ($reassigned && $ticket->assigned_to_id) {
            Notification::send_to($ticket->assigned_to_id, 'ticket_assigned', ['ticket' => $ticket, 'project' => $ticket->project]);
        }

        return true;
    }

    /**
     * Queues the custom field values to be saved and returns the history changes.
     */
    private function updateCustomFields(): array
    {
        $changes = [];

        foreach ($this->customFields as [$field, $value]) {
            $existing = $this->ticket->custom_field_value($field->id);

            if (!$existing) {
                $this->queue[] = new CustomFieldValue([
                    'custom_field_id' => $field->id,
                    'ticket_id' => $this->ticket->id,
                    'value' => $value
                ]);
            } elseif ($existing->value != $value) {
                $changes[] = [
                    'property' => $field->name,
                    'custom_field' => true,
                    'from' => $existing->value,
                    'to' => $value
                ];

                $existing->value = $value;
                $this->queue[] = $existing;
            }
        }

        $this->customFields = [];

        return $changes;
    }

    /**
     * Finds the record a reference field points to, adding an error to the
     * ticket if the value isn't allowed.
     *
     * @return \Avalon\Database\Model|null|false The record, null if the field is
     *                                             cleared or isn't a model, false if invalid.
     */
    private function resolve(string $field, ?int $value, int $projectId): mixed
    {
        $errorKey = self::RELATIONS[$field][0] ?? 'assigned_to';

        if ($value === null) {
            if (in_array($field, self::NULLABLE, true)) {
                return null;
            }
        } elseif ($field === 'assigned_to_id') {
            if ($this->isProjectMember($value, $projectId)) {
                return null;
            }
        } else {
            $target = self::RELATIONS[$field][1]::find($value);

            if ($target && (!in_array($field, self::PROJECT_SCOPED, true) || (int) $target->project_id === $projectId)) {
                return $target;
            }
        }

        $this->ticket->_add_error($errorKey, l('errors.invalid_value'));

        return false;
    }

    /**
     * Returns the change action and timeline action for a status moving
     * between different states (open, started, closed).
     *
     * @return array{0: string|null, 1: string}
     */
    private static function statusTransition(Status $from, Status $to): array
    {
        if ($from->isClosed()) {
            return ['reopen', 'ticket_reopened'];
        }

        if ($to->isClosed()) {
            return ['close', 'ticket_closed'];
        }

        // Open to started, or started back to open.
        return $from->isOpen() ? ['start', 'ticket_started'] : [null, 'ticket_updated'];
    }

    private function isProjectMember(int $userId, int $projectId): bool
    {
        return (bool) UserRole::select('id')
            ->where('project_id', $projectId)
            ->where('user_id', $userId)
            ->exec()
            ->row_count();
    }

    /**
     * Queues an uploaded file to be saved with the ticket.
     *
     * @param array $file Entry from `$_FILES`.
     *
     * @return array|null The history change, or null if it can't be attached.
     */
    private function queueAttachment(array $file): ?array
    {
        if (!$this->user->permission($this->ticket->project_id, 'add_attachments')) {
            return null;
        }

        $name = basename($file['name']);

        $this->queue[] = new Attachment([
            'name' => $name,
            'contents' => base64_encode(file_get_contents($file['tmp_name'])),
            'type' => (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: 'application/octet-stream',
            'size' => $file['size'],
            'user_id' => $this->user->id,
            'ticket_id' => $this->ticket->id
        ]);

        return [
            'property' => 'attachment',
            'from' => null,
            'to' => $name,
            'action' => 'add_attachment'
        ];
    }
}
