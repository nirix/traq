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

use Avalon\Http\Router;
use Traq\Controllers\Admin\Plugins;
use Traq\Controllers\Attachments;
use Traq\Controllers\ErrorController;
use Traq\Controllers\MiscController;
use Traq\Controllers\ProfileController;
use Traq\Controllers\ProjectController;
use Traq\Controllers\ProjectSettings\SettingsController;
use Traq\Controllers\SearchController;
use Traq\Controllers\SubscriptionsController;
use Traq\Controllers\TicketHistoryController;
use traq\controllers\Tickets;
use Traq\Controllers\TimelineController;
use traq\controllers\Usercp;
use Traq\Controllers\Wiki;

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
Router::register('api.auth', '/api/auth', ['\Traq\Controllers\ApiController', 'auth']);
Router::register('api.auth.project', '/api/auth/' . PROJECT_SLUG, ['\Traq\Controllers\ApiController', 'auth']);
Router::register('api.types', '/api/types', ['\Traq\Controllers\ApiController', 'types']);
Router::register('api.type', '/api/types/(?P<type_id>[0-9]+)', ['\Traq\Controllers\ApiController', 'type']);
Router::register('api.statuses', '/api/statuses', ['\Traq\Controllers\ApiController', 'statuses']);
Router::register('api.priorities', '/api/priorities', ['\Traq\Controllers\ApiController', 'priorities']);
Router::register('api.components', '/api/' . PROJECT_SLUG . '/components', ['\Traq\Controllers\ApiController', 'components']);
Router::register('api.customFields', '/api/' . PROJECT_SLUG . '/custom-fields', ['\Traq\Controllers\ApiController', 'customFields']);
Router::register('api.projectMembers', '/api/' . PROJECT_SLUG . '/members', ['\Traq\Controllers\ApiController', 'projectMembers']);
Router::register('search', '/api/search', [SearchController::class, 'search']);

// Misc
Router::register('misc.javascript', '/_js(?:.js)?', [MiscController::class, 'javascript']);
Router::register('autocomplete.username', '/_ajax/autocomplete/username', [MiscController::class, 'autocompleteUsername']);
Router::add('/_misc/ticket_tasks_bit', 'traq::controllers::TicketTasks.form_bit');

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
Router::register('ticket.history.edit', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/history/(?P<id>[0-9]+)/edit', [TicketHistoryController::class, 'edit']);
Router::register('ticket.history.delete', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/history/(?P<id>[0-9]+)/delete', [TicketHistoryController::class, 'delete']);
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/tasks/manage', 'traq::controllers::TicketTasks.manage/$2');
Router::add('/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/tasks/([0-9]+)', 'traq::controllers::TicketTasks.toggle/$2,$3');
Router::add('/' . PROJECT_SLUG . '/tickets/mass-actions', 'traq::controllers::Tickets.mass_actions');
Router::register('tickets', '/' . PROJECT_SLUG . '/tickets', [Tickets::class, 'index']);
Router::register('api.tickets', '/api/' . PROJECT_SLUG . '/tickets', [Tickets::class, 'action_api']);

// Wiki routes
Router::register('wiki.new', '/' . PROJECT_SLUG . '/wiki/_new', [Wiki::class, 'create']);
Router::register('wiki.edit', '/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)/_edit', [Wiki::class, 'edit']);
Router::register('wiki.main', '/' . PROJECT_SLUG . '/wiki', [Wiki::class, 'view'], ['slug' => 'main']);
Router::register('wiki.delete', '/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)/_delete', [Wiki::class, 'delete']);
Router::register('wiki.revisions', '/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)/_revisions', [Wiki::class, 'revisions']);
Router::register('wiki.revision', '/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)/_revisions/(?P<revision>\d+)', [Wiki::class, 'revision']);
Router::register('wiki.pages', '/' . PROJECT_SLUG . '/wiki/_pages', [Wiki::class, 'pages']);
Router::register('wiki.view', '/' . PROJECT_SLUG . '/wiki/(?P<slug>[a-zA-Z0-9\-\_/]+)', [Wiki::class, 'view']);

// Project settings routes
Router::register('project.settings', '/' . PROJECT_SLUG . '/settings', [SettingsController::class, 'index']);
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
