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

namespace traq\libraries\scm;

/**
 * SCM Base class.
 * Copyright (C) Jack Polgar
 *
 * @author Jack P.
 * @copyright (C) Jack P.
 * @package Traq
 * @package SCM
 * @version 0.1
 */
class File
{
    // This class should store the following information
    public $path; // File path.
    public $type; // Directory (dir) or file.
    public $size; // File size (in bytes)
    public $revision; // Last revision.

    /**
     * SCM File constructor.
     *
     * @param array $info File/directory info.
     */
    public function __construct($info)
    {
        // Assign the info to accessible variables
        foreach ($info as $key => $val) {
            $this->$key = $val;
        }

        if ($this->type == 'dir' and substr($this->path, -1) != '/') {
            $this->path = "{$this->path}/";
        }
    }

    /**
     * Returns the file name without directories.
     *
     * @return string
     */
    public function name()
    {
        $bits = explode("/", $this->path);
        return end($bits);
    }

    /**
     * Returns the file extension from the path name.
     *
     * @return mixed
     */
    public function ext()
    {
        if ($this->type == 'dir') {
            return false;
        }

        $bits = explode(".", $this->name());
        unset($bits[0]);

        // Check if this extension uses two dots.
        if (count($bits) > 1 and in_array(end($bits), array('gz'))) {
            return implode(".", $bits);
        }

        return end($bits);
    }

    /**
     * Checks if the file is a directory and if
     * its the root directory or not.
     *
     * @return bool
     */
    public function is_root()
    {
        return $this->type == 'dir' and ($this->path == '/' or $this->path == '');
    }

    /**
     * Checks if the file is a directory.
     *
     * @return bool
     */
    public function is_dir()
    {
        return $this->type == 'dir';
    }
}
