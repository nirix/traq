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
    protected int $assigned_to_id;
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
    protected string $priority;
    protected int $priority_id;
    protected string $severity;
    protected int $severity_id;

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
        return get_object_vars($this);
    }
}
