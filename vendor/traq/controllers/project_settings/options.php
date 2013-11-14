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

namespace traq\controllers\ProjectSettings;

use avalon\http\Request;
use avalon\output\View;

/**
 * Project settings controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Options extends AppController
{
    /**
     * Project options / information page.
     */
    public function action_index()
    {
        // Clone the project model so nothing
        // funky happens when there are errors
        // with the new information.
        $project = clone $this->project;

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Update the information
            $project->set(array(
                'name'         => Request::post('name', $project->name),
                'slug'         => Request::post('slug', $project->slug),
                'codename'     => Request::post('codename', $project->codename),
                'info'         => Request::post('info', $project->info),
                'displayorder' => Request::post('displayorder', $project->displayorder),
                'default_ticket_type_id' => Request::post('default_ticket_type_id', $project->default_ticket_type_id),
                'default_ticket_sorting' => Request::post('default_ticket_sorting', $project->default_ticket_sorting)
            ));

            // Set enable_wiki
            if ($this->is_api) {
                $project->enable_wiki = Request::post('enable_wiki', $project->enable_wiki);
            } else {
                $project->enable_wiki = Request::post('enable_wiki', 0);
            }

            // Check if the data is valid
            if ($project->is_valid()) {
                // Save and redirect
                $project->save();

                if ($this->is_api) {
                    return \API::response(1, array('project' => $project));
                } else {
                    Request::redirectTo($project->href('settings'));
                }
            }
        }

        View::set('proj', $project);
    }
}
