<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Traq.io
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

namespace Traq\Controllers\ProjectSettings;

use Avalon\Http\Request;
use Avalon\Output\View;

/**
 * Project settings controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class SettingsController extends AppController
{

    public function __construct()
    {
        $this->render['layout'] = false;

        parent::__construct();
    }

    /**
     * Project options / information page.
     */
    public function index()
    {
        if (Request::method() == 'POST') {
            return $this->save();
        }

        View::set('proj', $this->project);
        return $this->render('project_settings/index.phtml');
    }

    public function save()
    {
        // Clone the project model so nothing funky happens when there are errors with the new information.
        $project = clone $this->project;
        View::set('proj', $project);

        // Update the information
        $project->set([
            'name'         => Request::get('name', $project->name),
            'slug'         => Request::get('slug', $project->slug),
            'codename'     => Request::get('codename', $project->codename),
            'info'         => Request::get('info', $project->info),
            'displayorder' => Request::get('displayorder', $project->displayorder),
            'default_ticket_type_id' => Request::get('default_ticket_type_id', $project->default_ticket_type_id),
            'default_ticket_sorting' => Request::get('default_ticket_sorting', $project->default_ticket_sorting)
        ]);

        // Set enable_wiki
        if ($this->isApi) {
            $project->enable_wiki = Request::get('enable_wiki', $project->enable_wiki);
        } else {
            $project->enable_wiki = Request::get('enable_wiki', 0);
        }

        // Check if the data is valid
        if ($project->is_valid()) {
            // Save and redirect
            $project->save();

            if ($this->isApi) {
                return $this->json(['project' => $project]);
            } else {
                return Request::redirectTo($project->href('settings'));
            }
        }

        return $this->render('project_settings/index.phtml');
    }
}
