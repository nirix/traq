<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace Traq\Controllers;

use Radium\Http\Request;
use Traq\Models\Ticket;
use Traq\Models\Milestone;
use Traq\Models\Type;
use Traq\Models\Status;
use Traq\API;

/**
 * Project controller.
 *
 * @author Jack P.
 * @since 3.0
 */
class Projects extends AppController
{
    /**
     * Project listing page.
     */
    public function indexAction()
    {
        return $this->respondTo(function($format){
            if ($format == 'html') {
                return $this->render('projects/index.phtml');
            } elseif ($format == 'json') {
                return API::response(200, $this->projects);
            }
        });
    }

    /**
     * Handles the project info page.
     */
    public function showAction()
    {
        // Make sure this is a project
        if (!$this->project) {
            return $this->show404();
        }

        // Get open and closed ticket counts.
        $this->set('ticket_count', [
            'open'   => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 0)->rowCount(),
            'closed' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 1)->rowCount()
        ]);

        return $this->respondTo(function($format){
            if ($format == 'html') {
                return $this->render('projects/show.phtml');
            } elseif ($format == 'json') {
                return API::response(200, $this->project);
            }
        });
    }

    /**
     * Handles the changelog page.
     */
    public function action_changelog()
    {
        // Atom feed
        $this->feeds[] = array(Request::requestUri() . ".atom", l('x_changelog_feed', $this->project->name));

        // Fetch ticket types
        $types = array();
        foreach (Type::fetch_all() as $type) {
            $types[$type->id] = $type;
        }

        View::set('milestones', $this->project->milestones->where('status', 2)->order_by('displayorder', 'DESC')->exec()->fetch_all());
        View::set('types', $types);
    }
}
