<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

define("RTR_PROJSLUG", '(?P<project_slug>[a-zA-Z0-9\-\_]+)');

Router::add('root', 'Projects::index');
Router::add('/(login|logout|register|usercp)', 'Users::$1');

// ------------------------------------------------
// Project routes
Router::add('/' . RTR_PROJSLUG . '/tickets/(?P<ticket_id>[0-9]+)', 'Tickets::view/$2');
Router::add('/' . RTR_PROJSLUG . '/tickets', 'Tickets::index');
Router::add('/' . RTR_PROJSLUG . '/(timeline|roadmap)', 'Projects::$2');
Router::add('/' . RTR_PROJSLUG, 'Projects::view');

// Wiki routes
Router::add('/' . RTR_PROJSLUG . '/wiki', 'Wiki::index');
Router::add('/' . RTR_PROJSLUG . '/wiki/_pages', 'Wiki::pages');
Router::add('/' . RTR_PROJSLUG . '/wiki/_new', 'Wiki::new');
Router::add('/' . RTR_PROJSLUG . '/wiki/([a-zA-Z0-9\-\_]+)', 'Wiki::view/$2');
Router::add('/' . RTR_PROJSLUG . '/wiki/([a-zA-Z0-9\-\_]+)/_edit', 'Wiki::edit/$2');
Router::add('/' . RTR_PROJSLUG . '/wiki/([a-zA-Z0-9\-\_]+)/_delete', 'Wiki::delete/$2');

// Project settings routes
Router::add('/' . RTR_PROJSLUG . '/settings', 'Projects::Settings::index');
Router::add('/' . RTR_PROJSLUG . '/settings/(milestones|components)', 'Projects::$2::index');
Router::add('/' . RTR_PROJSLUG . '/settings/(milestones|components)/new', 'Projects::$2::new');
Router::add('/' . RTR_PROJSLUG . '/settings/(milestones|components)/([0-9]+)/(edit|delete)', 'Projects::$2::$4/$3');

// ------------------------------------------------
// AdminCP routes
Router::add('/admin', 'Admin::Projects::index');
Router::add('/admin/settings', 'Admin::Settings::index');

// Projects
Router::add('/admin/projects/new', 'Admin::Projects::new');
Router::add('/admin/projects/([0-9]+)/delete', 'Admin::Projects::delete/$1');

// Plugins
Router::add('/admin/plugins', 'Admin::Plugins::index');
Router::add('/admin/plugins/(enable|disable)/([a-zA-Z0-9\-\_]+)', 'Admin::Plugins::$1/$2');

// Users
Router::add('/admin/users', 'Admin::Users::index');
Router::add('/admin/users/new', 'Admin::Users::new');
Router::add('/admin/users/([0-9]+)/(edit|delete)', 'Admin::Users::$2/$1');

// User groups
Router::add('/admin/groups', 'Admin::Groups::index');
Router::add('/admin/groups/new', 'Admin::Groups::new');
Router::add('/admin/groups/([0-9]+)/(edit|delete)', 'Admin::Groups::$2/$1');

// Ticket types
Router::add('/admin/tickets/types', 'Admin::TicketTypes::index');
Router::add('/admin/tickets/types/new', 'Admin::TicketTypes::new');
Router::add('/admin/tickets/types/([0-9]+)/(edit|delete)', 'Admin::TicketTypes::$2/$1');

// Ticket statuses
Router::add('/admin/tickets/statuses', 'Admin::TicketStatuses::index');
Router::add('/admin/tickets/statuses/new', 'Admin::TicketStatuses::new');
Router::add('/admin/tickets/statuses/([0-9]+)/(edit|delete)', 'Admin::TicketStatuses::$2/$1');