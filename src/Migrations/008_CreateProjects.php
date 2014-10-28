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

class CreateProjects extends Migration
{
    public function up()
    {
        $this->createTable('projects', function($t){
            $t->varchar('name', array('nullable' => false));
            $t->varchar('slug', array('nullable' => false));
            $t->varchar('codename');
            $t->longtext('info');
            $t->int('next_ticket_id', array('nullable' => false, 'default' => 1));
            $t->bool('enable_wiki', array('nullable' => false, 'default' => true));
            $t->int('default_ticket_type_id', array('nullable' => false, 'default' => 1));
            $t->varchar('default_ticket_sorting', array('nullable' => false, 'default' => 'priority.asc'));
            $t->int('display_order', array('nullable' => false, 'default' => 0));
            $t->varchar('private_key');
            $t->timestamps();
        });
    }

    public function down()
    {
        $this->dropTable('projects');
    }
}
