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

namespace traq\libraries\scm\adapters;

/**
 * Git class.
 * Copyright (C) Jack Polgar
 *
 * @author Jack P.
 * @copyright (C) Jack P.
 * @package Traq
 * @package SCM
 * @version 0.1
 */
class Git extends \traq\libraries\SCM
{
    protected $_name = 'Git';
    protected $_binary = 'git';

    /**
     * Used when saving repository information.
     *
     * @param array $info Repository model object.
     * @param bool $is_new
     *
     * @return object
     */
    public function _before_save_info(&$repo, $is_new = false)
    {
        // Check if the location is a repository or not...
        if (!$resp = $this->_shell('branch') or preg_match("/Not a git repository/i", $resp)) {
            $repo->_add_error('location', l('errors.scm.location_not_a_repository'));
        }

        return $repo;
    }

    /**
     * Runs the specified command.
     *
     * @param string $cmd
     *
     * @return string
     */
    protected function _shell($cmd)
    {
        return parent::_shell("--git-dir \"{$this->info->location}\" {$cmd}");
    }

    /**
     * Returns the default/main branch of the repository.
     *
     * @return string
     */
    public function default_branch()
    {
        $branches = $this->branches();

        // Check if the master branch exists...
        if (in_array('master', $branches)) {
            return 'master';
        }
        // Use whatever the first branch in the list is.
        else {
            return $branches[0];
        }
    }

    /**
     * Returns an array of branches.
     *
     * @return array
     */
    public function branches()
    {
        return explode("\n", $this->_shell('branch'));
    }

    /**
     * Returns an array of tags.
     *
     * @return array
     */
    public function tags()
    {
        return explode("\n", $this->_shell('tag'));
    }
}
