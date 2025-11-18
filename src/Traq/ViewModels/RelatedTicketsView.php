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
    protected int $ticket_id;
    protected string $summary;
    protected int $relation_type_id;
    protected string $relation_type_name;
    protected string $priority_id;
    protected int $project_id;
    protected string $project_slug;

    public function __get(string $name): mixed
    {
        return $this->{$name};
    }

    public function href(string $uri = ''): string
    {
        return "/{$this->project_slug}/tickets/{$this->ticket_id}" . ($uri !== null ? '/' . trim($uri, '/') : '');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'summary' => $this->summary,
            'relation_type_id' => $this->relation_type_id,
            'relation_type_name' => $this->relation_type_name,
            'priority_id' => $this->priority_id,
            'project_id' => $this->project_id,
            'project_slug' => $this->project_slug,
        ];
    }
}
