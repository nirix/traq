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
use Traq\Models\Status;

/**
 * Admin Statuses controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class StatusesController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('statuses'));
    }

    public function index(): Response
    {
        $statuses = Status::fetchAll();

        if ($this->isJson) {
            return $this->json(['statuses' => $statuses]);
        }

        return $this->render('admin/statuses/index', ['statuses' => $statuses]);
    }

    /**
     * New status page.
     */
    public function new(): Response
    {
        $this->title(l('new'));

        // Create a new status object.
        $status = new Status;

        // Check if the form has been submitted.
        if (Request::method() == 'POST') {
            // Set the information.
            $status->set(array(
                'name'      => Request::get('name'),
                'status'    => Request::get('status'),
                'changelog' => Request::get('changelog', 0)
            ));

            // Check if the data is valid.
            if ($status->is_valid()) {
                // Save and redirect.
                $status->save();
                if ($this->isJson) {
                    return $this->json(['status' => $status]);
                } else {
                    return $this->redirectTo('/admin/tickets/statuses');
                }
            }
        }

        // Send the data to the view.
        if (Request::get('overlay') === 'true') {
            $view = 'new.overlay.phtml';
        } else {
            $view = 'new.phtml';
        }

        return $this->render("admin/statuses/{$view}", ['status' => $status]);
    }

    /**
     * Edit status page.
     *
     * @param integer $id
     */
    public function edit(int $id): Response
    {
        $this->title(l('edit'));

        // Fetch the status
        $status = Status::find($id);

        // Check if the form has been submitted.
        if (Request::method() == 'POST') {
            // Set the information.
            $status->set(array(
                'name'   => Request::get('name', $status->name),
                'status' => Request::get('status', $status->status)
            ));

            // Set changelog value
            if ($this->isApi) {
                $status->changelog = Request::get('changelog', $status->changelog);
            } else {
                $status->changelog = isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0;
            }

            // Check if the data is valid.
            if ($status->is_valid()) {
                // Save and redirect.
                $status->save();
                if ($this->isJson) {
                    return $this->json(['status' => $status]);
                } else {
                    return $this->redirectTo('/admin/tickets/statuses');
                }
            }
        }

        // Send the data to the view.
        if (Request::get('overlay') === 'true') {
            $view = 'edit.overlay.phtml';
        } else {
            $view = 'edit.phtml';
        }

        return $this->render("admin/statuses/{$view}", ['status' => $status]);
    }

    /**
     * Delete status page.
     *
     * @param integer $id
     */
    public function delete(int $id): Response
    {
        // Fetch the status, delete it and redirect.
        $status = Status::find($id)->delete();
        if ($this->isJson) {
            return $this->json(['success' => true]);
        } else {
            return $this->redirectTo('/admin/tickets/statuses');
        }
    }
}
