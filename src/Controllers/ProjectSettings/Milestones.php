<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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
use Traq\Models\Timeline;
use Traq\Traits\Controllers\CRUD;

/**
 * Milestones controller.
 *
 * @package Traq\Controllers\ProjectSettings
 * @author Jack P.
 * @since 3.0.0
 */
class Milestones extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\Milestone';
    protected $viewsDir = 'project_settings/milestones';

    // Singular and plural form
    protected $singular = 'milestone';
    protected $plural   = 'milestones';

    // Redirect route names
    protected $afterCreateRedirect  = 'project_settings_milestones';
    protected $afterSaveRedirect    = 'project_settings_milestones';
    protected $afterDestroyRedirect = 'project_settings_milestones';

    // Route names
    protected $newRoute = 'project_settings_new_milestone';
    protected $editRoute = 'project_settings_edit_milestone';

    /**
     * @var Milestone
     */
    protected $object;

    public function __construct()
    {
        parent::__construct();
        $this->addCrumb($this->translate('milestones'), $this->generateUrl('project_settings_milestones'));

        $this->before(['edit', 'save', 'delete', 'destroy'], function () {
            $this->object = Milestone::find(Request::$properties->get('id'));

            if (!$this->object || $this->object->project_id != $this->currentProject['id']) {
                return $this->show404();
            }
        });

        $this->after('save', function () {
            if ($this->object->isBeingCompleted) {
                Timeline::milestoneCompletedEvent($this->currentUser, $this->object)->save();
            }
        });
    }

    /**
     * Override to only get the relevant projects milestones.
     *
     * @return array
     */
    protected function getAllRows()
    {
        return Milestone::select('id', 'name', 'codename', 'status')
            ->where('project_id = ?')
            ->orderBy('display_order', 'ASC')
            ->setParameter(0, $this->currentProject['id'])
            ->execute()
            ->fetchAll();
    }

    public function destroyAction()
    {
        throw new \Exception("Not destroying milestone, need to handle deletion of tickets first.");
    }

    /**
     * @return array
     */
    protected function modelParams()
    {
        return $this->removeNullValues([
            'name'          => Request::$post->get('name'),
            'slug'          => Request::$post->get('slug'),
            'codename'      => Request::$post->get('codename'),
            'due'           => Request::$post->get('due'),
            'status'        => Request::$post->get('status'),
            'info'          => Request::$post->get('info'),
            'changelog'     => Request::$post->get('changelog'),
            'display_order' => Request::$post->get('display_order'),
            'project_id'    => $this->currentProject['id']
        ]);
    }
}
