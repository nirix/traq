<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

namespace Installer\Helpers\Upgrade;

/**
 * Version upgrades base class.
 *
 * @author Jack P.
 * @since 3.3
 * @package Traq
 * @subpackage Installer
 */
class Base
{
    /**
     * Upgrade version numbers.
     *
     * @return array
     */
    public static function revisions()
    {
        return static::$revisions;
    }

    /**
     * All the upgrades until it reaches the same revision
     * as `$db_revision`.
     *
     * @param object  $db          The database connection object.
     * @param integer $db_revision Revision to upgrade to.
     */
    public static function run($db, $db_revision)
    {
        foreach (static::revisions() as $revision) {
            if ($db_revision < $revision) {
                call_user_func_array(array(get_called_class(), "v{$revision}"), array($db));
            }
        }
    }
}
