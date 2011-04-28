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

Router::connect('/', 'Projects::index');

// Users
Router::connect('/login', 'User::login');
Router::connect('/register', 'User::register');

// Admin
Router::connect('admincp/:controller/:action', 'AdminCP::$1::$2');

// Projects
Router::connect('/:any/roadmap', 'Projects::roadmap');
Router::connect('/:any/timeline', 'Projects::timeline');
Router::connect('/:any/milestone/:any', 'Projects::milestone');
Router::connect('/:any/tickets', 'Tickets::index');
Router::connect('/:any/tickets/:num', 'Tickets::view');
Router::connect('/:any', 'Projects::view');
