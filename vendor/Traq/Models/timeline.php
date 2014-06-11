<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

namespace traq\models;

use avalon\database\Model;

/**
 * Timeline model.
 *
 * @package Traq
 * @subpackage Models
 * @since 3.0
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Timeline extends Model
{
    protected static $_name = 'timeline';
    protected static $_properties = array(
        'id',
        'project_id',
        'owner_id',
        'action',
        'data',
        'user_id',
        'created_at'
    );

    protected static $_belongs_to = array('user');

    /**
     * If the row is a ticket change, the ticket
     * object is returned.
     *
     * @return object
     */
    public function ticket()
    {
        if (!isset($this->_ticket)) {
            $this->_ticket = Ticket::find($this->owner_id);
        }
        return $this->_ticket;
    }

    /**
     * If the row is a milestone event, the milestone
     * object is returned.
     *
     * @return object
     */
    public function milestone()
    {
        if (!isset($this->_milestone)) {
            $this->_milestone = Milestone::find($this->owner_id);
        }
        return $this->_milestone;
    }

    public function is_valid()
    {
        return true;
    }

    /**
     * If the row is a ticket change, the new status
     * object is returned.
     *
     * @return object
     */
    public function ticket_status()
    {
        if (!isset($this->_ticket_status)) {
            $this->_ticket_status = Status::find($this->data);
        }
        return $this->_ticket_status;
    }

    /**
     * If the row is a moved ticket event, returns the other project.
     *
     * @return object
     */
    public function other_project() {
        if (!isset($this->_other_project)) {
            $this->_other_project = Project::find($this->data);
        }
        return $this->_other_project;
    }

    /**
     * If the row is a wiki page event, return the wiki page object.
     *
     * @return object
     */
    public function wiki_page() {
        if (!isset($this->_wiki_page)) {
            $this->_wiki_page = WikiPage::find($this->owner_id);
        }
        return $this->_wiki_page;
    }
}
