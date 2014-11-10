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
use Traq\Models\Severity;

/**
 * Severities controller
 *
 * @author Jack P.
 * @since 3.0.0
 */
class Severities extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('severities'));
    }

    /**
     * Severity listing.
     */
    public function indexAction()
    {
        $severities = Severity::all();

        return $this->respondTo(function($format) use ($severities) {
            if ($format == 'html') {
                return $this->render('admin/severities/index.phtml', [
                    'severities' => $severities
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($severities);
            }
        });
    }

    /**
     * New severity.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        if ($this->isOverlay) {
            return $this->render('admin/severities/new.overlay.phtml', [
                'severity' => new Severity
            ]);
        } else {
            return $this->render('admin/severities/new.phtml', [
                'severity' => new Severity
            ]);
        }
    }

    /**
     * Create severity.
     */
    public function createAction()
    {
        $severity = new Severity($this->severityParams());

        if ($severity->save()) {
            $this->redirectTo('admin/severities');
        } else {
            return $this->render('admin/severities/new.phtml', [
                'severity' => $severity
            ]);
        }
    }

    /**
     * Edit severity.
     *
     * @param integer $id
     */
    public function action_edit($id)
    {
        // Get the severity
        $severity = Severity::find($id);

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Set the name
            $severity->set('name', Request::post('name', $severity->name));

            // Save and redirect
            if ($severity->save()) {
                if ($this->is_api) {
                    return \API::response(1, array('severity' => $severity));
                } else {
                    Request::redirectTo('/admin/severities');
                }
            }
        }

        View::set('severity', $severity);
    }

    /**
     * Delete severity.
     *
     * @param integer $id
     */
    public function action_delete($id)
    {
        // Get the severity and delete
        $severity = Severity::find($id)->delete();

        if ($this->is_api) {
            return \API::response(1);
        } else {
            Request::redirectTo('/admin/severities');
        }
    }

    /**
     * @return array
     */
    protected function severityParams()
    {
        return [
            'name'  => Request::post('name'),
            'level' => Request::post('level')
        ];
    }
}
