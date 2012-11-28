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

namespace traq\controllers\ProjectSettings;

/**
 * Project settings controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AppController extends \traq\controllers\AppController
{
    /**
     * Constructor!
     */
    public function __construct()
    {
        parent::__construct();

        // Add 'Settings' to the page title
        $this->title(l('settings'));

        // Make sure this is a project and the user
        // has the correct permission to access the area.
        if (!$this->project
        or (!$this->user->permission($this->project->id, 'project_settings') and !$this->user->group->is_admin)) {
            $this->show_no_permission();
        }
    }
}
