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

use avalon\http\Request;
use avalon\output\View;

use traq\models\Repository;

use traq\libraries\SCM;

/**
 * Project repository settings controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Repositories extends AppController
{
    public function __construct()
    {
        parent::__construct();
        View::set('scm_types', scm_types());
        $this->title(l('repositories'));
    }

    /**
     * Lists the projects repositories.
     */
    public function action_index()
    {
        $repos = Repository::select()->where('project_id', $this->project->id);
        View::set('repos', $repos);
    }

    /**
     * New repository page.
     */
    public function action_new()
    {
        $repo = new Repository(array('type' => 'git'));

        // Check if the form has been submitted.
        if (Request::method() == 'post') {
            // Set the information
            $repo->set(array(
                'slug'       => Request::$post['slug'],
                'type'       => Request::$post['type'],
                'location'   => Request::$post['location'],
                'project_id' => $this->project->id
            ));

            // Get the SCM class
            $scm = SCM::factory($repo->type, $repo);

            // Runs its before save info method
            $scm->_before_save_info($repo, true);

            // Check if data is good
            if ($repo->is_valid()) {
                // Save and redirect
                $repo->save();
                Request::redirectTo($this->project->href('settings/repositories'));
            }
        }

        // Pass the repo info to the view.
        View::set('repo', $repo);
    }
}
