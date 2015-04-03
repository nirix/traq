<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
 * https://github.com/nirix
 * https://traq.io
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
use Traq\Models\Component;

/**
 * Components controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\ProjectSettings
 */
class Components extends AppController
{
    /**
     * @var Component
     */
    protected $component;

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('components'));

        $this->before(['edit', 'save', 'destroy'], function() {
            $this->component = Component::find(Request::$request['id']);

            if (!$this->component || $this->component->project_id != $this->project->id) {
                return $this->show404();
            }
        });
    }

    /**
     * Components listing page.
     */
    public function indexAction()
    {
        $components = Component::all();

        return $this->respondTo(function($format) use ($components) {
            if ($format == "html") {
                return $this->render("project_settings/components/index.phtml", [
                    'components' => $components
                ]);
            } elseif ($format == "json") {
                return $this->jsonResponse($components);
            }
        });
    }

    /**
     * New component page.
     */
    public function newAction()
    {
        $this->title($this->translate("new"));

        $component = new Component;

        if ($this->isOverlay) {
            return $this->render("project_settings/components/new.overlay.phtml", [
                'component' => $component
            ]);
        } else {
            return $this->render("project_settings/components/new.phtml", [
                'component' => $component
            ]);
        }
    }

    /**
     * Create component.
     */
    public function createAction()
    {
        $this->title($this->translate("new"));

        $component = new Component($this->componentParams());

        if ($component->save()) {
            return $this->respondTo(function($format) use ($component) {
                if ($format == "html") {
                    return $this->redirectTo("project_settings_components");
                } elseif ($format == "json") {
                    return $this->jsonResponse($component);
                }
            });
        } else {
            return $this->render("project_settings/components/new.phtml", [
                'component' => $component
            ]);
        }
    }

    /**
     * Edit component page.
     */
    public function editAction()
    {
        $this->title($this->translate("edit"));

        if ($this->isOverlay) {
            return $this->render("project_settings/components/edit.overlay.phtml", [
                'component' => $this->component
            ]);
        } else {
            return $this->render("project_settings/components/edit.phtml", [
                'component' => $this->component
            ]);
        }
    }

    /**
     * Save component.
     */
    public function saveAction()
    {
        $this->title($this->translate("edit"));

        $this->component->set($this->componentParams());

        if ($this->component->save()) {
            return $this->respondTo(function($format) {
                if ($format == "html") {
                    return $this->redirectTo("project_settings_components");
                } elseif ($format == "json") {
                    return $this->jsonResponse($this->component);
                }
            });
        } else {
            return $this->render("project_settings/components/edit.phtml", [
                'component' => $this->component
            ]);
        }
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

        if ($this->is_api) {
            return \API::response(1);
        } else {
            Request::redirectTo($this->project->href("settings/components"));
        }
    }

    /**
     * @return array
     */
    protected function componentParams()
    {
        return [
            'name'       => Request::post('name'),
            'project_id' => $this->project->id
        ];
    }
}
