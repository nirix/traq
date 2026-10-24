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
use Traq\Models\Milestone;

/**
 * Milestones controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class MilestonesController extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->title(l('milestones'));
    }

    /**
     * Milestones listing page.
     */
    public function index(): Response
    {
        $milestones = $this->project->milestones->order_by('displayorder', 'DESC');

        if ($this->isJson) {
            return $this->json([
                'milestones' => $milestones,
            ]);
        }

        return $this->render('project_settings/milestones/index.phtml', [
            'milestones' => $milestones,
        ]);
    }

    /**
     * New milestone page.
     */
    public function new(): Response
    {
        $this->title(l('new'));

        $milestone = new Milestone();

        $view = Request::get('overlay') === 'true' ? 'new.overlay.phtml' : 'new.phtml';

        return $this->render('project_settings/milestones/' . $view, [
            'milestone' => $milestone,
        ]);
    }

    /**
     * Create milestone.
     */
    public function create(): Response
    {
        $this->title(l('create'));

        $milestone = new Milestone();

        // Set the information
        $milestone->set(array(
            'name'         => Request::get('name'),
            'slug'         => Request::get('slug'),
            'codename'     => Request::get('codename'),
            'info'         => Request::get('info'),
            'due'          => Request::get('due') != '' ? Request::get('due') : null,
            'project_id'   => $this->project->id,
            'displayorder' => Request::get('displayorder') == '' ? 0 : Request::get('displayorder')
        ));

        // Check if the data is valid
        if ($milestone->is_valid()) {
            // Save and redirect
            $milestone->save();

            if ($this->isApi) {
                return $this->json([
                    'milestone' => $milestone,
                ]);
            } else {
                return $this->redirectTo($this->project->href('settings/milestones'));
            }
        }

        return $this->render('project_settings/milestones/new.phtml', [
            'milestone' => $milestone,
        ]);
    }

    /**
     * Edit milestone page.
     *
     * @param integer $id Milestone ID
     */
    public function edit(int $id): Response
    {
        $this->title(l('edit'));

        // Fetch the milestone
        $milestone = Milestone::find($id);

        if ($milestone->project_id !== $this->project->id) {
            return $this->renderNoPermission();
        }

        $view = Request::get('overlay') === 'true' ? 'edit.overlay.phtml' : 'edit.phtml';

        return $this->render('project_settings/milestones/' . $view, [
            'milestone' => $milestone,
        ]);
    }

    /**
     * Update milestone.
     */
    public function update(int $id): Response
    {
        $this->title(l('update'));

        $milestone = Milestone::find($id);

        // Update the information
        $milestone->set(array(
            'name'         => Request::get('name', $milestone->name),
            'slug'         => Request::get('slug', $milestone->slug),
            'codename'     => Request::get('codename', $milestone->codename),
            'info'         => Request::get('info', $milestone->info),
            'due'          => Request::get('due') != '' ? Request::get('due') : null,
            'status'       => Request::get('status', $milestone->status),
            'displayorder' => Request::get('displayorder', $milestone->displayorder) == ''
                ? 0
                : Request::get('displayorder', $milestone->displayorder)
        ));

        // Make sure the data is valid
        if ($milestone->is_valid()) {
            // Save and redirect
            $milestone->save();

            if ($this->isApi) {
                return $this->json([
                    'milestone' => $milestone,
                ]);
            } else {
                return $this->redirectTo($this->project->href('settings/milestones'));
            }
        }

        return $this->render('project_settings/milestones/edit.phtml', [
            'milestone' => $milestone,
        ]);
    }

    /**
     * Delete milestone page.
     *
     * @param integer $id Milestone ID
     */
    public function delete(int $id): Response
    {
        $this->title(l('delete'));

        // Fetch the milestone
        $milestone = Milestone::find($id);

        if ($milestone->project_id !== $this->project->id) {
            return $this->renderNoPermission();
        }

        $milestones = $this->project->milestones->order_by('displayorder', 'DESC')->exec()->fetch_all();
        $options = [];
        foreach ($milestones as $milestone) {
            // Skip the current milestone
            if ($milestone->id === $id) {
                continue;
            }

            $options[] = [
                'value' => $milestone->id,
                'label' => $milestone->name,
            ];
        }

        return $this->render('project_settings/milestones/delete.phtml', [
            'milestone' => $milestone,
            'milestoneOptions' => $options,
        ]);
    }

    /**
     * Perform delete milestone.
     */
    public function performDelete(int $id): Response
    {
        $this->title(l('delete'));

        $milestone = Milestone::find($id);

        // Move tickets
        $this->db->update('tickets')->set(['milestone_id' => Request::get('milestone')])->where('milestone_id', $id)->exec();

        // Delete milestone
        $milestone->delete();

        // Redirect
        if ($this->isApi) {
            return $this->json([
                'success' => true,
            ]);
        }

        return $this->redirectTo($this->project->href('settings/milestones'));
    }
}
