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

    public function __get(string $name): mixed
    {
        if ($name === 'direct') {
            return $this->direct;
        }

        if ($this->direct) {
            $name = 'related_' . $name;
        }

        // dd($field);

        return $this->{$name};
    }

    public function href(string $uri = ''): string
    {
        if ($this->direct) {
            return "/{$this->project_slug}/tickets/{$this->related_ticket_id}" . ($uri !== null ? '/' . trim($uri, '/') : '');
        }

        return "/{$this->related_project_slug}/tickets/{$this->ticket_id}" . ($uri !== null ? '/' . trim($uri, '/') : '');
    }
}
