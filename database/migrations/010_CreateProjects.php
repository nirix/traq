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

class CreateProjects extends Migration
{
    public function up()
    {
        $this->createTable("projects", function ($t) {
            $t->addColumn("name", "string");
            $t->addColumn("slug", "string");
            $t->addColumn("codename", "string", ['notnull' => false]);
            $t->addColumn("info", "text", ['notnull' => false]);
            $t->addColumn("next_ticket_id", "bigint", ['default' => 1]);
            $t->addColumn("enable_wiki", "boolean", ['default' => true]);
            $t->addColumn("default_ticket_type_id", "integer", ['default' => 1]);
            $t->addColumn("default_ticket_sorting", "string", ['default' => "priority.asc"]);
            $t->addColumn("display_order", "integer", ['default' => 0]);
            $t->addColumn("private_key", "string", ['notnull' => false]);

            $this->timestamps($t);
        });
    }

    public function down()
    {
        $this->dropTable("projects");
    }
}
