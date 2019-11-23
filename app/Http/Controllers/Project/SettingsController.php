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
use Traq\Models\Project;

/**
 * Project settings controller
 *
 * @package Traq\Controllers\ProjectSettings
 * @author Jack P.
 * @since 3.0.0
 */
class Options extends AppController
{
    /**
     * Project options / information page.
     */
    public function indexAction()
    {
        return $this->render('project_settings/options/index.phtml', ['proj' => $this->currentProject]);
    }

    /**
     * Save project.
     */
    public function saveAction()
    {
        $project = Project::find($this->currentProject['id']);
        $project->set($this->projectParams());

        if ($project->save()) {
            return $this->redirectTo('project_settings');
        } else {
            return $this->render('project_settings/options/index.phtml', [
                'proj' => $project
            ]);
        }
    }

    /**
     * @return array
     */
    protected function projectParams()
    {
        return [
            'name'                   => Request::$post['name'],
            'slug'                   => Request::$post['slug'],
            'codename'               => Request::$post['codename'],
            'info'                   => Request::$post['info'],
            'enable_wiki'            => (bool) Request::$post->get('enable_wiki', false),
            'default_ticket_type_id' => Request::$post['default_ticket_type_id'],
            'default_ticket_sorting' => Request::$post['default_ticket_sorting'],
            'display_order'          => (int) Request::$post->get('display_order', 0)
        ];
    }
}
