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
use Traq\Models\Milestone;

/**
 * Milestones controller
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\ProjectSettings
 */
class Milestones extends AppController
{
    /**
     * @var Milestone
     */
    protected $milestone;

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('milestones'));

        $this->before(['edit', 'save', 'delete', 'destroy'], function () {
            $this->milestone = Milestone::find(Request::$request['id']);

            if (!$this->milestone || $this->milestone->project_id != $this->project->id) {
                return $this->show404();
            }
        });
    }

    /**
     * Milestones listing page.
     */
    public function indexAction()
    {
        $milestones = Milestone::all();

        return $this->respondTo(function ($format) use ($milestones) {
            if ($format == "html") {
                return $this->render("project_settings/milestones/index.phtml", [
                    'milestones' => $milestones
                ]);
            } elseif ($format == "json") {
                return $this->jsonResponse($milestones);
            }
        });
    }

    /**
     * New milestone page.
     */
    public function newAction()
    {
        $this->title($this->translate("new"));

        $milestone = new Milestone(['display_order' => 0]);

        if ($this->isOverlay) {
            return $this->render("project_settings/milestones/new.overlay.phtml", [
                'milestone' => $milestone
            ]);
        } else {
            return $this->render("project_settings/milestones/new.phtml", [
                'milestone' => $milestone
            ]);
        }
    }

    /**
     * Create milestone.
     */
    public function createAction()
    {
        $this->title($this->translate("new"));

        $milestone = new Milestone($this->milestoneParams());

        if ($milestone->save()) {
            return $this->respondTo(function ($format) use ($milestone) {
                if ($format == "html") {
                    return $this->redirectTo("project_settings_milestones");
                } elseif ($format == "json") {
                    return $this->jsonResponse($milestone);
                }
            });
        } else {
            return $this->render("project_settings/milestones/new.phtml", [
                'milestone' => $milestone
            ]);
        }
    }

    /**
     * Edit milestone page.
     */
    public function editAction()
    {
        $this->title($this->translate("edit"));

        if ($this->isOverlay) {
            return $this->render("project_settings/milestones/edit.overlay.phtml", [
                'milestone' => $this->milestone
            ]);
        } else {
            return $this->render("project_settings/milestones/edit.phtml", [
                'milestone' => $this->milestone
            ]);
        }
    }

    /**
     * Create milestone.
     */
    public function saveAction()
    {
        $this->title($this->translate("edit"));

        $this->milestone->set($this->milestoneParams());

        if ($this->milestone->save()) {
            return $this->respondTo(function ($format) {
                if ($format == "html") {
                    return $this->redirectTo("project_settings_milestones");
                } elseif ($format == "json") {
                    return $this->jsonResponse($this->milestone);
                }
            });
        } else {
            return $this->render("project_settings/milestones/edit.phtml", [
                'milestone' => $this->milestone
            ]);
        }
    }

    /**
     * Delete milestone page.
     *
     * @param integer $id Milestone ID
     */
    public function action_delete($id)
    {
        $this->title(l('delete'));

        // Fetch the milestone
        $milestone = Milestone::find($id);

        if ($milestone->project_id !== $this->project->id) {
            return $this->show_no_permission();
        }

        // Fetch all but current milestone
        $milestones = array();
        $rows = Milestone::select()->where('id', $id, '!=')->where('status', '1')->exec()->fetch_all();
        foreach ($rows as $row) {
            $milestones[] = array('label' => $row->name, 'value' => $row->id);
        }

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Move tickets
            $this->db->update('tickets')->set(array('milestone_id' => Request::$post['milestone']))->where('milestone_id', $id)->exec();

            // Delete milestone
            $milestone->delete();

            // Redirect
            if ($this->is_api) {
                return \API::response(1);
            } else {
                Request::redirectTo("{$this->project->slug}/settings/milestones");
            }
        }

        View::set(compact('milestone', 'milestones'));
    }

    /**
     * @return array
     */
    protected function milestoneParams()
    {
        return [
            'name'          => Request::post('name'),
            'slug'          => Request::post('slug'),
            'codename'      => Request::post('codename'),
            'due'           => Request::post('due'),
            'status'        => Request::post('status'),
            'info'          => Request::post('info'),
            'changelog'     => Request::post('changelog'),
            'display_order' => Request::post('display_order'),
            'project_id'    => $this->project->id
        ];
    }
}
