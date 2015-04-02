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
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('milestones'));
    }

    /**
     * Milestones listing page.
     */
    public function indexAction()
    {
        $milestones = Milestone::all();

        return $this->respondTo(function($format) use ($milestones) {
            if ($format == 'html') {
                return $this->render('project_settings/milestones/index.phtml', [
                    'milestones' => $milestones
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($milestones);
            }
        });
    }

    /**
     * New milestone page.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        $milestone = new Milestone(['display_order' => 0]);

        if ($this->isOverlay) {
            return $this->render('project_settings/milestones/new.overlay.phtml', [
                'milestone' => $milestone
            ]);
        } else {
            return $this->render('project_settings/milestones/new.phtml', [
                'milestone' => $milestone
            ]);
        }
    }

    /**
     * Edit milestone page.
     *
     * @param integer $id Milestone ID
     */
    public function action_edit($id)
    {
        $this->title(l('edit'));

        // Fetch the milestone
        $milestone = Milestone::find($id);

        if ($milestone->project_id !== $this->project->id) {
            return $this->show_no_permission();
        }

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Update the information
            $milestone->set(array(
                'name'         => Request::post('name', $milestone->name),
                'slug'         => Request::post('slug', $milestone->slug),
                'codename'     => Request::post('codename', $milestone->codename),
                'info'         => Request::post('info', $milestone->info),
                'due'          => Request::post('due') != '' ? Request::post('due') : 'NULL',
                'status'       => Request::post('status', $milestone->status),
                'displayorder' => Request::post('displayorder', $milestone->displayorder)
            ));

            // Make sure the data is valid
            if ($milestone->is_valid()) {
                // Save and redirect
                $milestone->save();

                if ($this->is_api) {
                    return \API::response(1, array('milestone' => $milestone));
                } else {
                    Request::redirectTo("{$this->project->slug}/settings/milestones");
                }
            }
        }

        //View::set('milestone', $milestone);
        View::set(compact('milestone'));
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
}
