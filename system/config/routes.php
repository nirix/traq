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

Router::add('(<root>)', 'Projects::index');

// Users
Router::add('login', 'Users::login');
Router::add('register', 'Users::register');

// Admin
Router::add('admincp/:controller/:action', 'AdminCP::$1::$2');

// Projects
Router::add('(<project_slug>)/roadmap', 'Projects::roadmap');
Router::add('(<project_slug>)/timeline', 'Projects::timeline');
Router::add('(<project_slug>)/milestone/(<milestone_slug>)', 'Projects::milestone');
Router::add('(<project_slug>)/tickets', 'Tickets::index');
Router::add('(<project_slug>)/tickets/:num', 'Tickets::view');
Router::add('(<project_slug>)/', 'Projects::view');