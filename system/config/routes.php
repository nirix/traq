<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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

Router::add('root', 'Projects::index');

Router::add('/(login|logout|register)', 'Users::$1');

// Project controllers
Router::add('/(?P<project_slug>[\w]+)/tickets/(?P<ticket_id>[0-9]+)', 'Tickets::view/$2');
Router::add('/(?P<project_slug>[\w]+)/ticket-(?P<ticket_id>[0-9]+)', 'Tickets::view/$2');
Router::add('/(?P<project_slug>[\w]+)/tickets', 'Tickets::index');
Router::add('/(?P<project_slug>[\w]+)/(timeline|roadmap)', 'Projects::$1');
Router::add('/(?P<project_slug>[\w]+)', 'Projects::view');