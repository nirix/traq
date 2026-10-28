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

use Avalon\Http\Request;
use Traq\Models\Project;
use Traq\Models\User;

/**
 * Reads the ticket values a user submitted, leaving out the
 * properties they don't have permission to set or change.
 *
 * @author Jack P.
 * @package Traq
 * @subpackage Helpers
 * @since 3.9.0
 */
class TicketInput
{
    /**
     * Properties chosen from a list, as ticket field => request key.
     */
    private const SELECTS = [
        'type_id'        => 'type',
        'milestone_id'   => 'milestone',
        'version_id'     => 'version',
        'component_id'   => 'component',
        'severity_id'    => 'severity',
        'priority_id'    => 'priority',
        'status_id'      => 'status',
        'assigned_to_id' => 'assigned_to',
    ];

    /**
     * Properties typed in, as ticket field => request key.
     */
    private const TEXTS = [
        'summary'       => 'summary',
        'time_proposed' => 'time_proposed',
        'time_worked'   => 'time_worked',
    ];

    public function __construct(private User $user, private Project $project) {}

    /**
     * Values for a new ticket, with defaults for anything that can't be set.
     */
    public function newTicket(): array
    {
        $data = [
            'summary'      => $this->text('summary'),
            'body'         => $this->text('description'),
            'user_id'      => $this->user->id,
            'project_id'   => $this->project->id,
            'type_id'      => (int) Request::get('type', 1),
            'severity_id'  => 4,
            'milestone_id' => null,
            'version_id'   => null,
            'component_id' => null,
            'tasks'        => [],
            'is_private'   => 0,
        ];

        foreach (self::SELECTS as $field => $key) {
            $value = Request::get($key);

            if ($field === 'type_id' || !is_scalar($value) || !$this->can('set', $key)) {
                continue;
            }

            $data[$field] = (int) $value ?: (in_array($field, TicketManager::NULLABLE, true) ? null : 0);
        }

        foreach (self::TEXTS as $field => $key) {
            if ($field !== 'summary' && $this->can('set', $key)) {
                $data[$field] = $this->text($key);
            }
        }

        if ($this->can('set', 'tasks')) {
            $data['tasks'] = $this->tasks() ?? [];
        }

        return $data;
    }

    /**
     * Values to change on an existing ticket.
     */
    public function changes(): array
    {
        $data = $this->changed(self::SELECTS + self::TEXTS);

        if ($this->can('change', 'tasks') && ($tasks = $this->tasks()) !== null) {
            $data['tasks'] = $tasks;
        }

        return $data;
    }

    /**
     * Values to change on many tickets at once, where `-1` means "leave unchanged".
     */
    public function bulkChanges(): array
    {
        return $this->changed(self::SELECTS, true);
    }

    /**
     * The submitted comment, if the user is allowed to comment.
     */
    public function comment(): ?string
    {
        $comment = Request::get('comment');

        if (!is_string($comment) || !$this->user->permission($this->project->id, 'comment_on_tickets')) {
            return null;
        }

        return $comment;
    }

    /**
     * The submitted custom field values by custom field id.
     */
    public function customFields(): array
    {
        $values = Request::post('custom_fields');

        return is_array($values) ? $values : [];
    }

    private function can(string $action, string $property): bool
    {
        return $this->user->permission($this->project->id, "ticket_properties_{$action}_{$property}");
    }

    /**
     * @param array<string, string> $properties Ticket field => request key.
     */
    private function changed(array $properties, bool $bulk = false): array
    {
        $data = [];

        foreach ($properties as $field => $key) {
            $value = Request::get($key);

            if (!is_scalar($value) || ($bulk && (int) $value === -1) || !$this->can('change', $key)) {
                continue;
            }

            $data[$field] = is_string($value) ? trim($value) : $value;
        }

        return $data;
    }

    private function text(string $key): ?string
    {
        $value = Request::get($key);

        return is_scalar($value) ? trim((string) $value) : null;
    }

    /**
     * Decodes the submitted tasks, dropping blank or malformed entries.
     *
     * @return array|null Null if there aren't any valid tasks to read.
     */
    private function tasks(): ?array
    {
        $json = Request::get('tasks');
        $decoded = is_string($json) ? json_decode($json, true) : null;

        if (!is_array($decoded)) {
            return null;
        }

        $tasks = [];
        foreach ($decoded as $task) {
            if (is_array($task) && isset($task['task']) && is_string($task['task']) && trim($task['task']) !== '') {
                $tasks[] = [
                    'task' => $task['task'],
                    'completed' => !empty($task['completed']),
                ];
            }
        }

        return $tasks;
    }
}
