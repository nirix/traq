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

namespace Traq\Controllers\ProjectSettings;

use Avalon\Http\Request;
use Avalon\Http\Response;
use Traq\Models\Component;

/**
 * Components controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ComponentsController extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->title(l('components'));
    }

    /**
     * Components listing page.
     */
    public function index(): Response
    {
        if ($this->isJson) {
            return $this->json([
                'components' => $this->project->components,
            ]);
        }

        return $this->render('project_settings/components/index.phtml', [
            'components' => $this->project->components,
        ]);
    }

    /**
     * New component page.
     */
    public function new(): Response
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
                    return $this->json([
                        'component' => $component,
                    ]);
                } else {
                    return $this->redirectTo($this->project->href('settings/components'));
                }
            }
        }

        $view = Request::get('overlay') === 'true' ? 'new.overlay.phtml' : 'new.phtml';

        return $this->render('project_settings/components/' . $view, [
            'component' => $component,
        ]);
    }

    /**
     * Edit component page.
     *
     * @param integer $id Component ID
     */
    public function edit(int $id): Response
    {
        $this->title(l('edit'));

        // Fetch the component
        $component = Component::find($id);

        if ($component->project_id !== $this->project->id) {
            return $this->renderNoPermission();
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
                    return $this->json([
                        'component' => $component,
                    ]);
                } else {
                    return $this->redirectTo($this->project->href('settings/components'));
                }
            }
        }

        $view = Request::get('overlay') === 'true' ? 'edit.overlay.phtml' : 'edit.phtml';

        return $this->render('project_settings/components/' . $view, [
            'component' => $component,
        ]);
    }

    /**
     * Delete component.
     *
     * @param integer $id Component ID
     */
    public function delete($id): Response
    {
        // Fetch the component
        $component = Component::find($id);

        if ($component->project_id !== $this->project->id) {
            return $this->renderNoPermission();
        }

        // Delete component
        $component->delete();

        if ($this->isApi) {
            return $this->json([
                'success' => true,
            ]);
        } else {
            return $this->redirectTo($this->project->href("settings/components"));
        }
    }
}
