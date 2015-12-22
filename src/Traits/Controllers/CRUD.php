<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
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

namespace Traq\Traits\Controllers;

use Avalon\Http\Controller;
use Avalon\Database\Model;

/**
 * This trait reduces the amount of code needed for simple Create, Read, Update and Delete
 * based controllers.
 *
 * @package Traq\Traits
 * @author  Jack P.
 * @since 4.0.0
 */
trait CRUD
{
    /**
     * Get all rows.
     *
     * @return array
     */
    protected function getAllRows()
    {
        $model = $this->model;
        return $model::all();
    }

    /**
     * Find the object by ID.
     *
     * @return Model
     */
    protected function findObject($id)
    {
        $model = $this->model;
        return $model::find($id);
    }

    /**
     * Get the object, or find it if it doesn't exist.
     *
     * @return Model
     */
    protected function getObject($id = null)
    {
        if (isset($this->object) && $this->object) {
            return $this->object;
        } else {
            return $this->object = $this->findObject($id);
        }
    }

    /**
     * Get singular form.
     *
     * @return string
     */
    protected function getSingular()
    {
        return $this->singular;
    }

    /**
     * Get plural form.
     *
     * @return string
     */
    protected function getPlural()
    {
        return $this->plural;
    }

    /**
     * Listing.
     *
     * @return \Avalon\Http\Response
     */
    public function indexAction()
    {
        $rows = $this->getAllRows();

        return $this->respondTo(function ($format) use ($rows) {
            if ($format == 'html') {
                return $this->render("{$this->viewsDir}/index.phtml", [
                    "{$this->getPlural()}" => $rows
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($rows);
            }
        });
    }

    /**
     * New row form.
     *
     * @return \Avalon\Http\Response
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        if ($this->isOverlay) {
            return $this->render("{$this->viewsDir}/new.overlay.phtml", [
                "{$this->getSingular()}" => new $this->model
            ]);
        } else {
            return $this->render("{$this->viewsDir}/new.phtml", [
                "{$this->getSingular()}" => new $this->model
            ]);
        }
    }

    /**
     * Create row.
     */
    public function createAction()
    {
        $this->title($this->translate('new'));

        $object = new $this->model;
        $object->set($this->modelParams());

        if ($object->save()) {
            return $this->redirectTo($this->afterCreateRedirect);
        } else {
            $this->set($this->getSingular(), $object);
            return $this->respondTo(function ($format) use ($object) {
                if ($format == "html") {
                    return $this->render("{$this->viewsDir}/new.phtml");
                } elseif ($format == "json") {
                    return $this->jsonResponse($object);
                }
            });
        }
    }

    /**
     * Edit row form.
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        $this->title($this->translate('edit'));

        // Find the row
        $object = $this->getObject($id);

        if ($this->isOverlay) {
            return $this->render("{$this->viewsDir}/edit.overlay.phtml", [
                "{$this->getSingular()}" => $object
            ]);
        } else {
            return $this->render("{$this->viewsDir}/edit.phtml", [
                "{$this->getSingular()}" => $object
            ]);
        }
    }

    /**
     * Save row.
     *
     * @param integer $id
     */
    public function saveAction($id)
    {
        $this->title($this->translate('edit'));

        // Find the row and update
        $object = $this->getObject($id);
        $object->set($this->modelParams());

        if ($object->save()) {
            return $this->redirectTo($this->afterSaveRedirect);
        } else {
            $this->set($this->getSingular(), $object);
            return $this->respondTo(function ($format) use ($object) {
                if ($format == "html") {
                    return $this->render("{$this->viewsDir}/edit.phtml");
                } elseif ($format == "json") {
                    return $this->jsonResponse($object);
                }
            });
        }
    }

    /**
     * Delete row.
     *
     * @param integer $id
     */
    public function destroyAction($id)
    {
        // Find the group, delete and redirect.
        $object = $this->getObject($id)->delete();

        return $this->respondTo(function ($format) use ($object) {
            if ($format == "html") {
                return $this->redirectTo($this->afterDestroyRedirect);
            } elseif ($format == "json") {
                return $this->jsonResponse([
                    'deleted' => true,
                    "{$this->getSingular()}" => $object->toArray()
                ]);
            }
        });
    }
}
