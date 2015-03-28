<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
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

namespace Traq\Controllers\Admin;

use Avalon\Http\Request;
use Traq\Models\Type;

/**
 * Admin Types controller
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers
 */
class Types extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('types'));
    }

    public function indexAction()
    {
        $types = Type::all();

        return $this->respondTo(function($format) use ($types) {
            if ($format == 'html') {
                return $this->render('admin/types/index.phtml', [
                    'types' => $types
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($types);
            }
        });
    }

    /**
     * New type page.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        if ($this->isOverlay) {
            return $this->render('admin/types/new.overlay.phtml', [
                'type' => new Type
            ]);
        } else {
            return $this->render('admin/types/new.phtml', [
                'type' => new Type
            ]);
        }
    }

    /**
     * Create type.
     */
    public function createAction()
    {
        $this->title($this->translate('new'));

        $type = new Type($this->typeParams());

        if ($type->save()) {
            return $this->redirectTo('admin_types');
        } else {
            $this->set('type', $type);
            return $this->respondTo(function($format) {
                if ($format == "html") {
                    return $this->render('admin/types/new.phtml', ['error' => true]);
                } elseif ($format == "json") {
                    return $this->jsonResponse($type);
                }
            });
        }
    }

    /**
     * Edit type.
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        $this->title($this->translate('edit'));

        // Find the type
        $type = Type::find($id);

        if ($this->isOverlay) {
            return $this->render('admin/types/edit.overlay.phtml', [
                'type' => $type
            ]);
        } else {
            return $this->render('admin/types/edit.phtml', [
                'type' => $type
            ]);
        }
    }

    /**
     * Save type.
     *
     * @param integer $id
     */
    public function saveAction($id)
    {
        $this->title($this->translate('edit'));

        // Fetch and update type
        $type = Type::find($id);
        $type->set($this->typeParams());

        if ($type->save()) {
            return $this->redirectTo('admin_types');
        } else {
            $this->set('type', $type);
            return $this->respondTo(function($format) use ($type) {
                if ($format == "html") {
                    return $this->render('admin/types/edit.phtml', ['error' => true]);
                } elseif ($format == "json") {
                    return $this->jsonResponse($type);
                }
            });
        }
    }

    /**
     * Delete type.
     *
     * @param integer $id
     */
    public function destroyAction($id)
    {
        // Find the type, delete and redirect.
        $type = Type::find($id)->delete();

        return $this->respondTo(function($format) use ($type) {
            if ($format == "html") {
                return $this->redirectTo('admin_types');
            } elseif ($format == "json") {
                return $this->jsonResponse([
                    'deleted' => true,
                    'type'    => $type->toArray()
                ]);
            }
        });
    }

    protected function typeParams()
    {
        return [
            'name'              => $this->request->post('name'),
            'bullet'            => $this->request->post('bullet'),
            'show_on_changelog' => $this->request->post('show_on_changelog', true),
            'template'          => $this->request->post('template')
        ];
    }
}
