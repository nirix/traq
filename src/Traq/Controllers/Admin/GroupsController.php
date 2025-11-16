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

namespace Traq\Controllers\Admin;

use Avalon\Http\Request;
use Avalon\Http\Response;
use Traq\Models\Group;

/**
 * Admin Groups controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class GroupsController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('groups'));
    }

    public function index(): Response
    {
        $groups = Group::fetchAll();

        if ($this->isJson) {
            return $this->json(['groups' => $groups]);
        }

        return $this->render('admin/groups/index', ['groups' => $groups]);
    }

    /**
     * New group page.
     */
    public function new(): Response
    {
        $this->title(l('new'));

        // Create a new group object.
        $group = new Group;

        // Check if the form has been submitted.
        if (Request::method() == 'POST') {
            // Set the groups name.
            $group->set('name', Request::$post['name']);
            $group->set('is_admin', 0);

            // Make sure the data is valid.
            if ($group->is_valid()) {
                $group->save();

                // Return API response
                if ($this->isApi) {
                    return $this->json(['group' => $group]);
                } else {
                    return $this->redirectTo('/admin/groups');
                }
            }
        }

        // Send the group object to the view.
        if (Request::get('overlay') === 'true') {
            $view = 'new.overlay.phtml';
        } else {
            $view = 'new.phtml';
        }

        return $this->render("admin/groups/{$view}", ['group' => $group]);
    }

    /**
     * Edit group page.
     *
     * @param integer $id Group ID.
     */
    public function edit(int $id): Response
    {
        $this->title(l('edit'));

        // Find the group.
        $group = Group::find($id);

        // Check if the form has been submitted.
        if (Request::method() == 'POST') {
            // Set the groups name
            $group->set('name', Request::$post['name']);

            // Make sure the data is valid.
            if ($group->is_valid()) {
                $group->save();

                // Return API response
                if ($this->isApi) {
                    return $this->json(['group' => $group]);
                } else {
                    return $this->redirectTo('/admin/groups');
                }
            }
        }

        // Send the group object to the view.
        if (Request::get('overlay') === 'true') {
            $view = 'edit.overlay.phtml';
        } else {
            $view = 'edit.phtml';
        }

        return $this->render("admin/groups/{$view}", ['group' => $group]);
    }

    /**
     * Delete group page.
     *
     * @param integer $id Group ID.
     */
    public function delete(int $id): Response
    {
        // Find the group, delete it and redirect
        $group = Group::find($id);
        $group->delete();

        // Return API response
        if ($this->isApi) {
            return $this->json(['success' => true]);
        } else {
            return $this->redirectTo('/admin/groups');
        }
    }
}
