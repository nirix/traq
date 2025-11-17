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
 * Related tickets view model.
 *
 * @package Traq
 * @subpackage ViewModels
 * @since 3.9.0
 *
 * Dynamic properties enabled for custom fields.
 */
class RelatedTicketsView
{
    protected int $id;
    protected ?int $ticket_id;
    protected ?string $summary;
    protected ?string $project_slug;
    protected ?int $priority_id;
    protected ?int $related_ticket_id;
    protected ?string $related_summary;
    protected ?int $related_priority_id;
    protected ?string $related_project_slug;
    protected bool $direct;
    protected int $relation_type_id;
    protected string $relation_type_name;

    public function __get(string $name): mixed
    {
        if ($name === 'relation_type_id') {
            return $this->relation_type_id;
        }

        if ($name === 'relation_type_name') {
            return $this->relation_type_name;
        }

        if ($name === 'direct') {
            return $this->direct;
        }

        if ($this->direct) {
            $name = 'related_' . $name;
        };

        return $this->{$name};
    }

    public function href(string $uri = ''): string
    {
        if ($this->direct) {
            return "/{$this->project_slug}/tickets/{$this->related_ticket_id}" . ($uri !== null ? '/' . trim($uri, '/') : '');
        }

        return "/{$this->related_project_slug}/tickets/{$this->ticket_id}" . ($uri !== null ? '/' . trim($uri, '/') : '');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'summary' => $this->summary,
            'priority_id' => $this->priority_id,
            'project_slug' => $this->project_slug,
            'related_ticket_id' => $this->related_ticket_id,
            'related_summary' => $this->related_summary,
            'related_priority_id' => $this->related_priority_id,
            'related_project_slug' => $this->related_project_slug,
            'direct' => $this->direct,
            'relation_type_id' => $this->relation_type_id,
            'relation_type_name' => $this->relation_type_name,
        ];
    }
}
