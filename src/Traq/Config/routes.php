<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

use avalon\http\Router;
use Traq\Controllers\Admin\Plugins;
use Traq\Controllers\Attachments;
use Traq\Controllers\ErrorController;
use Traq\Controllers\ProfileController;
use Traq\Controllers\ProjectController;
use Traq\Controllers\ProjectSettingsController;
use Traq\Controllers\SearchController;
use Traq\Controllers\SubscriptionsController;
use traq\controllers\Tickets;
use Traq\Controllers\TimelineController;
use traq\controllers\Usercp;

const PROJECT_SLUG = '(?P<project_slug>[a-zA-Z0-9\-\_]+)';

Router::register('root', 'root', [ProjectController::class, 'index']);

Router::register('errors.404', '404', [ErrorController::class, 'error404']);
Router::add('/(login|logout|register)', 'traq::controllers::Users.$1');
Router::add('/login/resetpassword', 'traq::controllers::Users.reset_password');
Router::add('/login/resetpassword/([a-zA-Z0-9]+)', 'traq::controllers::Users.reset_password/$1');
Router::register('usercp', '/usercp', [Usercp::class, 'action_index']);
Router::add('/usercp/(password|subscriptions|create_api_key)', 'traq::controllers::Usercp.$1');
Router::register('profile', '/users/(?P<id>[0-9]+)', [ProfileController::class, 'view']);
Router::add('/users/validate/(.*)', 'traq::controllers::Users.validate/$1');

// API
Router::add('/api/auth', 'traq::controllers::API.auth');
Router::add('/api/auth/' . PROJECT_SLUG, 'traq::controllers::API.auth');
Router::add('/api/types', 'traq::controllers::API.types');
Router::add('/api/statuses', 'traq::controllers::API.statuses');
Router::add('/api/priorities', 'traq::controllers::API.priorities');
Router::add('/api/' . PROJECT_SLUG . '/components', 'traq::controllers::API.components');
Router::add('/api/' . PROJECT_SLUG . '/custom-fields', 'traq::controllers::API.customFields');
Router::add('/api/' . PROJECT_SLUG . '/members', 'traq::controllers::API.projectMembers');
Router::register('search', '/api/search', [SearchController::class, 'search']);

// Misc
Router::add('/_js(?:.js)?', 'traq::controllers::Misc.javascript');
Router::add('/_ajax/ticket_template/([0-9]+)', 'traq::controllers::Misc.ticket_template/$1');
Router::register('autocomplete.username', '/_ajax/autocomplete/username', ['\traq\controllers\Misc', 'autocompleteUsername']);
Router::add('/_misc/preview_text', 'traq::controllers::Misc.preview_text');
Router::add('/_misc/ticket_tasks_bit', 'traq::controllers::TicketTasks.form_bit');
Router::add('/_misc/format_text', 'traq::controllers::Misc.format_text');

// Attachment routes
Router::register('attachments.view', '/attachments/(?P<attachment_id>[0-9]+)/([a-zA-Z0-9\-_.]+)', [Attachments::class, 'view']);
Router::register('attachments.delete', '/attachments/(?P<attachment_id>[0-9]+)/([a-zA-Z0-9\-_.\s]+)/delete', [Attachments::class, 'delete']);

// ------------------------------------------------
// Project routes
// Router::add('/projects', 'traq::controllers::Projects.index');
Router::register('projects', '/projects', [ProjectController::class, 'index']);
Router::register('project', '/' . PROJECT_SLUG, [ProjectController::class, 'view']);
Router::register('project.roadmap', '/' . PROJECT_SLUG . '/roadmap', [ProjectController::class, 'roadmap']);
Router::register('project.roadmap.filtered', '/' . PROJECT_SLUG . '/roadmap/(?<filter>completed|all|cancelled)', [ProjectController::class, 'roadmap']);
Router::register('project.changelog', '/' . PROJECT_SLUG . '/changelog', [ProjectController::class, 'changelog']);
Router::register('project.milestone', '/' . PROJECT_SLUG . '/milestone/(?P<milestone_slug>[a-zA-Z0-9\-_.]+?)', [ProjectController::class, 'viewMilestone']);
Router::register('timeline', '/' . PROJECT_SLUG . '/timeline', [TimelineController::class, 'index']);
Router::register('timeline.delete', '/' . PROJECT_SLUG . '/timeline/(?P<eventId>[0-9]+)/delete', [TimelineController::class, 'deleteEvent']);

// Ticket routes
Router::register('tickets.new', '/' . PROJECT_SLUG . '/tickets/new', [Tickets::class, 'action_new']);
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)', 'traq::controllers::Tickets.view/$2');
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/move', 'traq::controllers::Tickets.move/$2');
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/delete', 'traq::controllers::Tickets.delete/$2');
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/(update|edit|vote|voters)', 'traq::controllers::Tickets.$3/$2');
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/history/([0-9]+)/edit', 'traq::controllers::TicketHistory.edit/$3');
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/history/([0-9]+)/delete', 'traq::controllers::TicketHistory.delete/$3');
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/tasks/manage', 'traq::controllers::TicketTasks.manage/$2');
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/tasks/([0-9]+)', 'traq::controllers::TicketTasks.toggle/$2,$3');
Router::add('/' . PROJECT_SLUG . '/tickets/mass-actions', 'traq::controllers::Tickets.mass_actions');
Router::register('tickets', '/' . PROJECT_SLUG . '/tickets', [Tickets::class, 'index']);
Router::register('api.tickets', '/api/' . PROJECT_SLUG . '/tickets', [Tickets::class, 'action_api']);

// Wiki routes
Router::add('/' . PROJECT_SLUG . '/wiki', 'traq::controllers::Wiki.view', array('slug' => 'main'));
Router::add('/' . PROJECT_SLUG . '/wiki/_pages', 'traq::controllers::Wiki.pages');
Router::add('/' . PROJECT_SLUG . '/wiki/_new', 'traq::controllers::Wiki.new');
Router::add('/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)/_edit', 'traq::controllers::Wiki.edit');
Router::add('/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)/_delete', 'traq::controllers::Wiki.delete');
Router::add('/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)/_revisions', 'traq::controllers::Wiki.revisions/$2');
Router::add('/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)/_revisions/(\d+)', 'traq::controllers::Wiki.revision/$2,$3');
Router::add('/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)', 'traq::controllers::Wiki.view');

// Project settings routes
Router::register('project.settings', '/' . PROJECT_SLUG . '/settings', [ProjectSettingsController::class, 'index']);
Router::add('/' . PROJECT_SLUG . '/settings/(milestones|components|members)', 'traq::controllers::ProjectSettings::$2.index');
Router::add('/' . PROJECT_SLUG . '/settings/(milestones|components|members)/new', 'traq::controllers::ProjectSettings::$2.new');
Router::add('/' . PROJECT_SLUG . '/settings/(milestones|components|members)/([0-9]+)/(edit|delete)', 'traq::controllers::ProjectSettings::$2.$4/$3');
Router::add('/' . PROJECT_SLUG . '/settings/custom_fields', 'traq::controllers::ProjectSettings::CustomFields.index');
Router::add('/' . PROJECT_SLUG . '/settings/custom_fields/new', 'traq::controllers::ProjectSettings::CustomFields.new');
Router::add('/' . PROJECT_SLUG . '/settings/custom_fields/([0-9]+)/(edit|delete)', 'traq::controllers::ProjectSettings::CustomFields.$3/$2');

Router::add('/' . PROJECT_SLUG . '/settings/members/save', 'traq::controllers::ProjectSettings::Members.save');

// Project permission routes
Router::add('/' . PROJECT_SLUG . '/settings/permissions/(groups|roles)', 'traq::controllers::ProjectSettings::Permissions.index/$2');

// Subscription routes
Router::register('unsubscribe', '/unsubscribe/(?P<uuid>[\w\-]+)', [SubscriptionsController::class, 'unsubscribe']);
Router::register('project_subunsub', '/' . PROJECT_SLUG . '/(?:un)?subscribe', [SubscriptionsController::class, 'toggleProject']);
Router::register('milestone_subunsub', '/' . PROJECT_SLUG . '/milestone/(?P<milestone_slug>[a-zA-Z0-9\-_.]+?)/(?:un)?subscribe', [SubscriptionsController::class, 'toggleMilestone']);
Router::register('ticket_subunsub', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/(?:un)?subscribe', [SubscriptionsController::class, 'toggleTicket']);

// ------------------------------------------------
// AdminCP routes
Router::add('/admin', 'traq::controllers::admin::Dashboard.index');
Router::add('/admin/settings', 'traq::controllers::admin::Settings.index');

// Projects
Router::add('/admin/projects', 'traq::controllers::admin::Projects.index');
Router::add('/admin/projects/new', 'traq::controllers::admin::Projects.new');
Router::add('/admin/projects/([0-9]+)/delete', 'traq::controllers::admin::Projects.delete/$1');

// Plugins
Router::register('plugins', '/admin/plugins', [Plugins::class, 'index']);
Router::register('plugins.install', '/admin/plugins/(?P<file>[a-zA-Z0-9\-\_]+)/install', [Plugins::class, 'install']);
Router::register('plugins.enable', '/admin/plugins/(?P<file>[a-zA-Z0-9\-\_]+)/enable', [Plugins::class, 'enable']);
Router::register('plugins.disable', '/admin/plugins/(?P<file>[a-zA-Z0-9\-\_]+)/disable', [Plugins::class, 'disable']);
Router::register('plugins.uninstall', '/admin/plugins/(?P<file>[a-zA-Z0-9\-\_]+)/uninstall', [Plugins::class, 'uninstall']);

// Users
Router::add('/admin/users', 'traq::controllers::admin::Users.index');
Router::add('/admin/users/new', 'traq::controllers::admin::Users.new');
Router::add('/admin/users/([0-9]+)/(edit|delete)', 'traq::controllers::admin::Users.$2/$1');
Router::add('/admin/users/mass_actions', 'traq::controllers::admin::Users.mass_actions');

// User groups
Router::add('/admin/groups', 'traq::controllers::admin::Groups.index');
Router::add('/admin/groups/new', 'traq::controllers::admin::Groups.new');
Router::add('/admin/groups/([0-9]+)/(edit|delete)', 'traq::controllers::admin::Groups.$2/$1');

// Project roles
Router::add('/admin/roles', 'traq::controllers::admin::ProjectRoles.index');
Router::add('/admin/roles/new', 'traq::controllers::admin::ProjectRoles.new');
Router::add('/admin/roles/([0-9]+)/(edit|delete)', 'traq::controllers::admin::ProjectRoles.$2/$1');

// Ticket types
Router::add('/admin/tickets/types', 'traq::controllers::admin::Types.index');
Router::add('/admin/tickets/types/new', 'traq::controllers::admin::Types.new');
Router::add('/admin/tickets/types/([0-9]+)/(edit|delete)', 'traq::controllers::admin::Types.$2/$1');

// Ticket statuses
Router::add('/admin/tickets/statuses', 'traq::controllers::admin::Statuses.index');
Router::add('/admin/tickets/statuses/new', 'traq::controllers::admin::Statuses.new');
Router::add('/admin/tickets/statuses/([0-9]+)/(edit|delete)', 'traq::controllers::admin::Statuses.$2/$1');

// Severities
Router::add('/admin/severities', 'traq::controllers::admin::Severities.index');
Router::add('/admin/severities/new', 'traq::controllers::admin::Severities.new');
Router::add('/admin/severities/([0-9]+)/(edit|delete)', 'traq::controllers::admin::Severities.$2/$1');

// Priorities
Router::add('/admin/priorities', 'traq::controllers::admin::Priorities.index');
Router::add('/admin/priorities/new', 'traq::controllers::admin::Priorities.new');
Router::add('/admin/priorities/([0-9]+)/(edit|delete)', 'traq::controllers::admin::Priorities.$2/$1');
