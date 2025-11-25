<?php
/*!
 * Traq
 * Copyright (C) 2009-2022 Jack Polgar
 * Copyright (C) 2012-2022 Traq.io
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

namespace Traq\ViewModels;

/**
 * Ticket view model.
 *
 * @package Traq
 * @subpackage ViewModels
 * @since 3.9.0
 *
 * Dynamic properties enabled for custom fields.
 */
#[\AllowDynamicProperties]
class TicketView
{
    protected int $ticket_id;
    protected string $summary;
    protected int $user_id;
    protected ?int $assigned_to_id;
    protected int $votes;
    protected string $created_at;
    protected ?string $updated_at;
    protected bool $is_closed;
    protected string $owner;
    protected ?string $assignee;
    protected string $type;
    protected ?string $milestone;
    protected ?string $milestone_slug;
    protected ?string $version;
    protected ?string $version_slug;
    protected ?string $component;
    protected string $status;
    protected int $status_id;
    protected string $priority;
    protected int $priority_id;
    protected string $severity;
    protected int $severity_id;
    protected int $project_id;
    protected string $project_slug;

    public function getTicketId(): int
    {
        return $this->ticket_id;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getAssignedToId(): int
    {
        return $this->assigned_to_id;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function getIsClosed(): bool
    {
        return $this->is_closed;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getAssignee(): ?string
    {
        return $this->assignee;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMilestone(): string
    {
        return $this->milestone;
    }

    public function getMilestoneSlug(): string
    {
        return $this->milestone_slug;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getVersionSlug(): ?string
    {
        return $this->version_slug;
    }

    public function getComponent(): ?string
    {
        return $this->component;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function toArray(): array
    {
        $data = [
            'ticket_id' => $this->ticket_id,
            'summary' => $this->summary,
            'user_id' => $this->user_id,
            'assigned_to_id' => $this->assigned_to_id,
            'votes' => $this->votes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_closed' => $this->is_closed,
            'owner' => [
                'name' => $this->owner,
            ],
            'assignee' => $this->assignee ? [
                'name' => $this->assignee,
            ] : null,
            'type' => [
                'name' => $this->type,
            ],
            'milestone' => $this->milestone ? [
                'name' => $this->milestone,
                'slug' => $this->milestone_slug,
            ] : null,
            'version' => $this->version ? [
                'name' => $this->version,
                'slug' => $this->version_slug,
            ] : null,
            'component' => $this->component ? [
                'name' => $this->component,
            ] : null,
            'status' => [
                'id' => $this->status_id,
                'name' => $this->status,
            ],
            'priority' => [
                'id' => $this->priority_id,
                'name' => $this->priority,
            ],
            'severity' => [
                'id' => $this->severity_id,
                'name' => $this->severity,
            ],
            'project' => [
                'slug' => $this->project_slug,
            ],
        ];

        return $data;
    }
}
