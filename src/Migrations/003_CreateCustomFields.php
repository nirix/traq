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

class CreateCustomFields extends Migration
{
    public function up()
    {
        $this->createTable('custom_fields', function($t){
            $t->varchar('name', array('nullable' => false));
            $t->varchar('slug', array('nullable' => false));
            $t->varchar('type', array('nullable' => false));
            $t->longtext('values');
            $t->tinyint('multiple', array('nullable' => false, 'default' => 0));
            $t->varchar('default_value');
            $t->varchar('regex');
            $t->int('min_length');
            $t->int('max_length');
            $t->bool('is_required', array('nullable' => false, 'default' => false));
            $t->int('project_id', array('nullable' => false));
            $t->varchar('ticket_type_ids', array('nullable' => false));
        });
    }

    public function down()
    {
        $this->dropTable('custom_fields');
    }
}
