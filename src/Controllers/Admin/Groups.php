<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
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

namespace Traq\Controllers\Admin;

use Avalon\Http\Request;
use Traq\Models\Group;

/**
 * Admin Groups controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers
 */
class Groups extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('groups'));
    }

    /**
     * Groups listing.
     */
    public function indexAction()
    {
        $groups = Group::all();

        return $this->respondTo(function($format) use ($groups) {
            if ($format == 'html') {
                return $this->render('admin/groups/index.phtml', [
                    'groups' => $groups
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($groups);
            }
        });
    }

    /**
     * New group page.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        if ($this->isOverlay) {
            return $this->render('admin/groups/new.overlay.phtml', [
                'group' => new Group
            ]);
        } else {
            return $this->render('admin/groups/new.phtml', [
                'group' => new Group
            ]);
        }
    }

    /**
     * Create group.
     */
    public function createAction()
    {
        $this->title($this->translate('new'));

        $group = new Group($this->groupParams());

        if ($group->save()) {
            return $this->redirectTo('admin_groups');
        } else {
            $this->set('group', $group);
            return $this->respondTo(function($format) {
                if ($format == "html") {
                    return $this->render('admin/groups/new.phtml');
                } elseif ($format == "json") {
                    return $this->jsonResponse($group);
                }
            });
        }
    }

    /**
     * Edit group page.
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        $this->title($this->translate('edit'));

        // Find the group
        $group = Group::find($id);

        if ($this->isOverlay) {
            return $this->render('admin/groups/edit.overlay.phtml', [
                'group' => $group
            ]);
        } else {
            return $this->render('admin/groups/edit.phtml', [
                'group' => $group
            ]);
        }
    }

    /**
     * Save group.
     *
     * @param integer $id
     */
    public function saveAction($id)
    {
        $this->title($this->translate('edit'));

        // Fetch and update group
        $group = Group::find($id);
        $group->set($this->groupParams());

        if ($group->save()) {
            return $this->redirectTo('admin_groups');
        } else {
            $this->set('group', $group);
            return $this->respondTo(function($format) use ($group) {
                if ($format == "html") {
                    return $this->render('admin/groups/edit.phtml');
                } elseif ($format == "json") {
                    return $this->jsonResponse($group);
                }
            });
        }
    }

    /**
     * Delete group page.
     *
     * @param integer $id
     */
    public function destroyAction($id)
    {
        // Find the group, delete and redirect.
        $group = Group::find($id)->delete();

        return $this->respondTo(function($format) use ($group) {
            if ($format == "html") {
                return $this->redirectTo('admin_groups');
            } elseif ($format == "json") {
                return $this->jsonResponse([
                    'deleted' => true,
                    'group'    => $group->toArray()
                ]);
            }
        });
    }

    /**
     * @return array
     */
    protected function groupParams()
    {
        return [
            'name'     => Request::post('name'),
            'is_admin' => Request::post('is_admin', false)
        ];
    }
}
