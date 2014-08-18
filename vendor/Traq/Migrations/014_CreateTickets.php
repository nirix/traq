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

class CreateTickets extends Migration
{
    public function up()
    {
        $this->createTable('tickets', function($t){
            $t->int('ticket_id', array('nullable' => false));
            $t->varchar('summary', array('nullable' => false));
            $t->longtext('body', array('nullable' => false));
            $t->int('user_id', array('nullable' => false));
            $t->int('project_id', array('nullable' => false));
            $t->int('milestone_id', array('default' => 0));
            $t->int('version_id');
            $t->int('component_id', array('nullable' => false));
            $t->int('type_id', array('nullable' => false));
            $t->int('status_id', array('nullable' => false, 'default' => 1));
            $t->int('priority_id', array('nullable' => false, 'default' => 3));
            $t->int('severity_id', array('nullable' => false));
            $t->int('assigned_to_id');
            $t->bool('is_closed', array('nullable' => false, 'default' => false));
            $t->bool('is_private', array('nullable' => false, 'default' => false));
            $t->int('votes', array('default' => 0));
            $t->longtext('tasks');
            $t->longtext('extra');
            $t->timestamps();
        });
    }

    public function down()
    {
        $this->dropTable('tickets');
    }
}
