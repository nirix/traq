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

namespace Traq\Database\Migrations;

use Avalon\Database\Migration;

class CreateTickets extends Migration
{
    public function up()
    {
        $this->createTable("tickets", function ($t) {
            $t->addColumn("ticket_id", "bigint");
            $t->addColumn("summary", "string");
            $t->addColumn("body", "text");
            $t->addColumn("user_id", "bigint");
            $t->addColumn("project_id", "bigint");
            $t->addColumn("milestone_id", "bigint", ['default' => 0]);
            $t->addColumn("version_id", "bigint", ['notnull' => false]);
            $t->addColumn("component_id", "integer", ['notnull' => false]);
            $t->addColumn("type_id", "integer");
            $t->addColumn("status_id", "integer", ['default' => 1]);
            $t->addColumn("priority_id", "integer", ['default' => 3]);
            $t->addColumn("severity_id", "integer", ['default' => 4]);
            $t->addColumn("assigned_to_id", "bigint", ['default' => 0, 'notnull' => false]);
            $t->addColumn("is_closed", "boolean", ['default' => false]);
            $t->addColumn("is_private", "boolean", ['default' => false]);
            $t->addColumn("votes", "integer", ['default' => 0]);
            $t->addColumn("tasks", "text", ['notnull' => false]);
            $t->addColumn("extra", "text", ['notnull' => false]);

            $this->timestamps($t);
        });
    }

    public function down()
    {
        $this->dropTable("tickets");
    }
}
