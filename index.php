<?php
/*
 * Traq
 * Copyright (C) 2009-2022 Jack Polgar
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

require __DIR__ . '/vendor/autoload.php';

if (class_exists('\Whoops\Run')) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

define('START_TIME', microtime(true));
define('START_MEM',  memory_get_usage());

require dirname(__FILE__) . '/vendor/bootstrap.php';

use Avalon\Core\Kernel as Avalon;

Avalon::init();
Avalon::run();
