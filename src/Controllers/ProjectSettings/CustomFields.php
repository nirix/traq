<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

namespace Traq\Controllers\ProjectSettings;

use Avalon\Http\Request;
use Traq\Models\CustomField;

/**
 * Custom fields controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\ProjectSettings
 */
class CustomFields extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('custom_fields'));
    }

    /**
     * Custom field listing.
     */
    public function indexAction()
    {
        $fields = CustomField::select()
            ->where('project_id = ?')
            ->orderBy('name', 'ASC')
            ->setParameter(0, $this->currentProject['id'])
            ->fetchAll();

        return $this->render('project_settings/custom_fields/index.phtml', [
            'fields' => $fields
        ]);
    }

    /**
     * New field form.
     */
    public function newAction()
    {
        return $this->render('project_settings/custom_fields/new.phtml', [
            'field' => new CustomField
        ]);
    }
}
