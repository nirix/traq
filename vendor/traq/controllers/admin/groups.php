<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

namespace traq\controllers\admin;

use avalon\http\Request;
use avalon\output\View;

use traq\models\Group;

/**
 * Admin Groups controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Groups extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('groups'));
    }

    public function action_index()
    {
        $groups = Group::fetch_all();
        View::set('groups', $groups);
    }

    /**
     * New group page.
     */
    public function action_new()
    {
        $this->title(l('new'));

        // Create a new group object.
        $group = new Group;

        // Check if the form has been submitted.
        if (Request::method() == 'post') {
            // Set the groups name.
            $group->set('name', Request::$post['name']);

            // Make sure the data is valid.
            if ($group->is_valid()) {
                $group->save();

                // Return API response
                if ($this->is_api) {
                    return \API::response(1, array('group' => $group));
                } else {
                    Request::redirect(Request::base('/admin/groups'));
                }
            }
        }

        // Send the group object to the view.
        View::set('group', $group);
    }

    /**
     * Edit group page.
     *
     * @param integer $id Group ID.
     */
    public function action_edit($id)
    {
        $this->title(l('edit'));

        // Find the group.
        $group = Group::find($id);

        // Check if the form has been submitted.
        if (Request::method() == 'post') {
            // Set the groups name
            $group->set('name', Request::$post['name']);

            // Make sure the data is valid.
            if ($group->is_valid()) {
                $group->save();

                // Return API response
                if ($this->is_api) {
                    return \API::response(1, array('group' => $group));
                } else {
                    Request::redirect(Request::base('/admin/groups'));
                }
            }
        }

        // Send the group object to the view.
        View::set('group', $group);
    }

    /**
     * Delete group page.
     *
     * @param integer $id Group ID.
     */
    public function action_delete($id)
    {
        // Find the group, delete it and redirect
        $group = Group::find($id);
        $group->delete();

        // Return API response
        if ($this->is_api) {
            return \API::response(1);
        } else {
            Request::redirect(Request::base('/admin/groups'));
        }
    }
}
