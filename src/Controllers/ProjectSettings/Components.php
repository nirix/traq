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

namespace Traq\Controllers\ProjectSettings;

use Avalon\Http\Request;
use Traq\Models\Component;
use Traq\Traits\Controllers\CRUD;

/**
 * Components controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\ProjectSettings
 */
class Components extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\Component';
    protected $viewsDir = 'project_settings/components';

    // Singular and plural form
    protected $singular = 'component';
    protected $plural   = 'components';

    // Redirect route names
    protected $afterCreateRedirect  = 'project_settings_components';
    protected $afterSaveRedirect    = 'project_settings_components';
    protected $afterDestroyRedirect = 'project_settings_components';

    /**
     * @var Component
     */
    protected $object;

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('components'));

        $this->before(['edit', 'save', 'destroy'], function () {
            $this->object = Component::find(Request::$properties->get('id'));

            if (!$this->object || $this->object->project_id != $this->currentProject['id']) {
                return $this->show404();
            }
        });
    }

    /**
     * Override to only get the relevant projects milestones.
     *
     * @return array
     */
    protected function getAllRows()
    {
        return Component::select('id', 'name')
            ->where('project_id = ?')
            ->orderBy('name', 'ASC')
            ->setParameter(0, $this->currentProject['id'])
            ->execute()
            ->fetchAll();
    }

    /**
     * @return array
     */
    protected function modelParams()
    {
        return [
            'name'       => Request::$post->get('name'),
            'project_id' => $this->currentProject['id']
        ];
    }
}
