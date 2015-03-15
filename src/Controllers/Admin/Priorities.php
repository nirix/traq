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
use Traq\Models\Priority;

/**
 * Admin Priorities controller.
 *
 * @author Jack P.
 * @since 3.0.0
 */
class Priorities extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('priorities'));
    }

    public function indexAction()
    {
        $priorities = Priority::all();

        return $this->respondTo(function($format) use ($priorities) {
            if ($format == 'html') {
                return $this->render('admin/priorities/index.phtml', [
                    'priorities' => $priorities
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($priorities);
            }
        });
    }

    /**
     * New priority page.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        if ($this->isOverlay) {
            return $this->render('admin/priorities/new.overlay.phtml', [
                'priority' => new Priority
            ]);
        } else {
            return $this->render('admin/priorities/new.phtml', [
                'priority' => new Priority
            ]);
        }
    }

    /**
     * Create priority.
     */
    public function createAction()
    {
        $priority = new Priority($this->priorityParams());

        if ($priority->save()) {
            return $this->redirectTo('admin/priorities');
        } else {
            return $this->render('admin/priorities/new.phtml', [
                'priority' => $priority
            ]);
        }
    }

    /**
     * Edit priority page.
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        $this->title($this->translate('edit'));

        $priority = Priority::find($id);

        if ($this->isOverlay) {
            return $this->render('admin/priorities/edit.overlay.phtml', [
                'priority' => $priority
            ]);
        } else {
            return $this->respondTo(function($format) use ($priority) {
                if ($format == 'html') {
                    return $this->render('admin/priorities/edit.phtml', [
                        'priority' => $priority
                    ]);
                } elseif ($format == 'json') {
                    return $this->jsonResponse($priority->toArray());
                }
            });
        }
    }

    /**
     * Save priority.
     *
     * @param integer $id
     */
    public function saveAction($id)
    {
        $priority = Priority::find($id);

        $priority->set($this->priorityParams());

        if ($priority->save()) {
            return $this->redirectTo('admin/priorities');
        } else {
            return $this->render('admin/priorities/edit.phtml', [
                'priority' => $priority
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
        $priority = Priority::find($id);
        $priority->delete();
        return $this->redirectTo("admin/priorities");
    }

    /**
     * @return array
     */
    protected function priorityParams()
    {
        return [
            'name' => Request::post('name')
        ];
    }
}
