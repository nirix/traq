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
use Traq\Models\Severity;

/**
 * Severities controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class SeveritiesController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('severities'));
    }

    /**
     * Severity listing.
     */
    public function index(): Response
    {
        $severities = Severity::fetchAll();

        if ($this->isJson) {
            return $this->json(['severities' => $severities]);
        }

        return $this->render('admin/severities/index', ['severities' => $severities]);
    }

    /**
     * New severity.
     */
    public function new(): Response
    {
        // Create the severity
        $severity = new Severity();

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Set the name
            $severity->set('name', Request::get('name'));

            // Save and redirect
            if ($severity->save()) {
                if ($this->isApi) {
                    return $this->json(['severity' => $severity]);
                } else {
                    return $this->redirectTo('/admin/severities');
                }
            }
        }

        if (Request::get('overlay') === 'true') {
            $view = 'new.overlay.phtml';
        } else {
            $view = 'new.phtml';
        }

        return $this->render("admin/severities/{$view}", ['severity' => $severity]);
    }

    /**
     * Edit severity.
     *
     * @param integer $id
     */
    public function edit(int $id): Response
    {
        // Get the severity
        $severity = Severity::find($id);

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Set the name
            $severity->set('name', Request::get('name', $severity->name));

            // Save and redirect
            if ($severity->save()) {
                if ($this->isApi) {
                    return $this->json(['severity' => $severity]);
                } else {
                    return $this->redirectTo('/admin/severities');
                }
            }
        }

        if (Request::get('overlay') === 'true') {
            $view = 'edit.overlay.phtml';
        } else {
            $view = 'edit.phtml';
        }

        return $this->render("admin/severities/{$view}", ['severity' => $severity]);
    }

    /**
     * Delete severity.
     *
     * @param integer $id
     */
    public function delete(int $id): Response
    {
        // Get the severity and delete
        $severity = Severity::find($id)->delete();

        if ($this->isApi) {
            return $this->json(['success' => true]);
        } else {
            return $this->redirectTo('/admin/severities');
        }
    }
}
