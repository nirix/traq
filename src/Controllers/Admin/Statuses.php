<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

use Radium\Http\Request;

use Traq\Models\Status;

/**
 * Admin Statuses controller.
 *
 * @author Jack P.
 * @since 3.0.0
 */
class Statuses extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('statuses'));

        $this->set('typeSelectOptions', [
            ['label' => $this->translate('status.type.1'), 'value' => 1],
            ['label' => $this->translate('status.type.2'), 'value' => 2],
            ['label' => $this->translate('status.type.0'), 'value' => 0]
        ]);
    }

    public function indexAction()
    {
        $statuses = Status::all();

        return $this->respondTo(function($format) use ($statuses) {
            if ($format == 'html') {
                return $this->render('admin/statuses/index.phtml', [
                    'statuses' => $statuses
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($statuses);
            }
        });
    }

    /**
     * New status page.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        if ($this->isOverlay) {
            return $this->render('admin/statuses/new.overlay.phtml', [
                'status' => new Status
            ]);
        } else {
            return $this->render('admin/statuses/new.phtml', [
                'status' => new Status
            ]);
        }
    }

    /**
     * Create status
     */
    public function createAction()
    {
        $status = new Status($this->statusParams());

        if ($status->save()) {
            $this->redirectTo('admin/statuses');
        } else {
            return $this->render('admin/statuses/new.phtml', [
                'status' => $status
            ]);
        }
    }

    /**
     * Edit status page.
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        $this->title($this->translate('edit'));

        $status = Status::find($id);

        if ($this->isOverlay) {
            return $this->render('admin/statuses/edit.overlay.phtml', [
                'status' => $status
            ]);
        } else {
            return $this->respondTo(function($format) use ($status) {
                if ($format == 'html') {
                    return $this->render('admin/statuses/edit.phtml', [
                        'status' => $status
                    ]);
                } elseif ($format == 'json') {
                    return $this->jsonResponse($status->toArray());
                }
            });
        }
    }

    /**
     * Save status.
     *
     * @param integer $id
     */
    public function saveAction($id)
    {
        $status = Status::find($id);

        $status->set($this->statusParams());

        if ($status->save()) {
            $this->redirectTo('admin/statuses');
        } else {
            return $this->render('admin/statuses/edit.phtml', [
                'status' => $status
            ]);
        }
    }

    /**
     * Delete status page.
     *
     * @param integer $id
     */
    public function destroyAction($id)
    {
        $status = Status::find($id);
        $status->delete();
        $this->redirectTo("admin/statuses");
    }

    /**
     * @return array
     */
    protected function statusParams()
    {
        return [
            'name'      => Request::post('name'),
            'status'    => Request::post('type'),
            'changelog' => Request::post('changelog', false)
        ];
    }
}
