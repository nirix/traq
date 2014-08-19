<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace Traq\Models;

use Radium\Database\Model;
use Traq\Traq;

/**
 * Plugin model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Plugin extends Model
{
    protected static $_validates = array(
        'directory'  => array('required', 'unique'),
        'version'    => array('required'),
        'autoload'   => array('required'),
        'main'       => array('required')
    );

    protected static $_after = array(
        'construct' => array('decodeAutoload')
    );

    /**
     * Registers the plugins autoload paths with the autoloader.
     */
    public function registerWithAutoloader()
    {
        // Register namespace with autoloader
        foreach ($this->autoload as $namespace => $directory) {
            Traq::registerNamespace(
                $namespace,
                VENDORDIR . "/{$this->directory}/{$directory}"
            );
        }
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->is_enabled == '1' ? true : false;
    }

    /**
     * @return array
     */
    public static function allEnabled()
    {
        return static::select()->where('is_enabled = ?', 1)->fetchAll();
    }

    public function decodeAutoload()
    {
        if (!$this->_isNew) {
            $this->autoload = json_decode($this->autoload, true);
        }
    }
}
