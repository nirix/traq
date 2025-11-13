<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

namespace traq\controllers\ProjectSettings;

use Avalon\Http\Request;
use Avalon\Output\View;
use traq\helpers\API;
use Traq\Models\Component;

/**
 * Components controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Components extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('components'));
    }

    /**
     * Components listing page.
     */
    public function action_index()
    {
        View::set('components', $this->project->components);
    }

    /**
     * New component page.
     */
    public function action_new()
    {
        $this->title(l('new'));

        $component = new Component();

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Set the information
            $component->set(array(
                'name'       => Request::get('name'),
                'project_id' => $this->project->id
            ));

            // Check if the data is valid
            if ($component->is_valid()) {
                // Save and redirect
                $component->save();
                if ($this->isApi) {
                    return API::response(1, array('component' => $component));
                } else {
                    Request::redirectTo("{$this->project->slug}/settings/components");
                }
            }
        }

        View::set('component', $component);
    }

    /**
     * Edit component page.
     *
     * @param integer $id Component ID
     */
    public function action_edit($id)
    {
        $this->title(l('edit'));

        // Fetch the component
        $component = Component::find($id);

        if ($component->project_id !== $this->project->id) {
            return $this->show_no_permission();
        }

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Update the information
            $component->set(array(
                'name' => Request::get('name'),
            ));

            // Check if the data is valid
            if ($component->is_valid()) {
                // Save and redirect
                $component->save();
                if ($this->isApi) {
                    return API::response(1, array('component' => $component));
                } else {
                    Request::redirectTo("{$this->project->slug}/settings/components");
                }
            }
        }

        View::set('component', $component);
    }

    /**
     * Delete component.
     *
     * @param integer $id Component ID
     */
    public function action_delete($id)
    {
        // Fetch the component
        $component = Component::find($id);

        if ($component->project_id !== $this->project->id) {
            return $this->show_no_permission();
        }

        // Delete component
        $component->delete();

        if ($this->isApi) {
            return API::response(1);
        } else {
            Request::redirectTo($this->project->href("settings/components"));
        }
    }
}
