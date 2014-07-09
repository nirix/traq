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

namespace Traq\Migrations;

use Radium\Database\Schema\Migration;

/**
 * Recreates the plugins table for the new plugin system.
 */
class RecreatePlugins extends Migration
{
    public function up()
    {
        $this->dropTable('plugins');

        $this->createTable('plugins', function($t){
            $t->varchar('name', array('nullable' => false));
            $t->text('description');
            $t->varchar('version', array('nullable' => false));
            $t->varchar('author', array('nullable' => false));
            $t->varchar('url', array('nullable' => false));
            $t->varchar('directory', array('nullable' => false));
            $t->varchar('file', array('nullable' => false));
            $t->varchar('namespace', array('nullable' => false));
            $t->varchar('class', array('nullable' => false));
            $t->tinyint('is_enabled', array(
                'nullable' => false,
                'default' => true,
                'length' => 1)
            );
        });
    }

    public function down()
    {
        throw new \Exception("The RecreatePlugins migration cannot be rolled back");
    }
}
