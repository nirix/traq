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
use Traq\Controllers\Admin\DashboardController;
use Traq\Controllers\Admin\GroupsController;
use Traq\Controllers\Admin\Plugins;
use Traq\Controllers\Admin\ProjectRolesController;
use Traq\Controllers\Admin\ProjectsController;
use Traq\Controllers\Admin\SettingsController as AdminSettingsController;
use Traq\Controllers\Admin\SeveritiesController;
use Traq\Controllers\Admin\StatusesController;
use Traq\Controllers\Admin\TypesController;
use Traq\Controllers\Admin\UsersController as AdminUsersController;
use Traq\Controllers\Attachments;
use Traq\Controllers\ErrorController;
use Traq\Controllers\MiscController;
use Traq\Controllers\ProfileController;
use Traq\Controllers\ProjectController;
use Traq\Controllers\ProjectSettings\ComponentsController;
use Traq\Controllers\ProjectSettings\CustomFieldsController;
use Traq\Controllers\ProjectSettings\MembersController;
use Traq\Controllers\ProjectSettings\MilestonesController;
use Traq\Controllers\ProjectSettings\PermissionsController;
use Traq\Controllers\ProjectSettings\SettingsController as ProjectSettingsController;
use Traq\Controllers\SearchController;
use Traq\Controllers\SubscriptionsController;
use Traq\Controllers\TicketController;
use Traq\Controllers\TicketHistoryController;
use Traq\Controllers\TicketTasksController;
use Traq\Controllers\TimelineController;
use Traq\Controllers\UserController;
use Traq\Controllers\UserCPController;
use Traq\Controllers\Wiki;

const PROJECT_SLUG = '(?P<project_slug>[a-zA-Z0-9\-\_]+)';

Router::register('root', 'root', [ProjectController::class, 'index']);

Router::register('errors.404', '404', [ErrorController::class, 'error404']);
// Router::add('/(login|logout|register)', 'traq::controllers::Users.$1');
Router::register('users.login', '/login', [UserController::class, 'login']);
Router::register('users.logout', '/logout', [UserController::class, 'logout']);
Router::register('users.register', '/register', [UserController::class, 'register']);
Router::register('users.validate', '/users/validate/(?P<key>[a-zA-Z0-9]+)', [UserController::class, 'validate']);
Router::register('users.resetPassword', '/login/resetpassword', [UserController::class, 'resetPassword']);
Router::register('users.resetPassword.key', '/login/resetpassword/(?P<key>[a-zA-Z0-9]+)', [UserController::class, 'resetPassword']);
Router::register('usercp', '/usercp', [UserCPController::class, 'index']);
Router::register('usercp.password', '/usercp/password', [UserCPController::class, 'password']);
Router::register('usercp.subscriptions', '/usercp/subscriptions', [UserCPController::class, 'subscriptions']);
Router::register('usercp.create_api_key', '/usercp/create_api_key', [UserCPController::class, 'createApiKey']);
Router::register('profile', '/users/(?P<id>[0-9]+)', [ProfileController::class, 'view']);

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
Router::register('ticket.tasks.form.bit', '/_misc/ticket_tasks_bit', [TicketTasksController::class, 'formBit']);

// Attachment routes
Router::register('attachments.view', '/attachments/(?P<attachment_id>[0-9]+)/([a-zA-Z0-9\-_.]+)', [Attachments::class, 'view']);
Router::register('attachments.delete', '/attachments/(?P<attachment_id>[0-9]+)/([a-zA-Z0-9\-_.\s]+)/delete', [Attachments::class, 'delete']);

// ------------------------------------------------
// Project routes
Router::register('projects', '/projects', [ProjectController::class, 'index']);
Router::register('project', '/' . PROJECT_SLUG, [ProjectController::class, 'view']);
Router::register('project.roadmap', '/' . PROJECT_SLUG . '/roadmap', [ProjectController::class, 'roadmap']);
Router::register('project.roadmap.filtered', '/' . PROJECT_SLUG . '/roadmap/(?<filter>completed|all|cancelled)', [ProjectController::class, 'roadmap']);
Router::register('project.changelog', '/' . PROJECT_SLUG . '/changelog', [ProjectController::class, 'changelog']);
Router::register('project.milestone', '/' . PROJECT_SLUG . '/milestone/(?P<milestone_slug>[a-zA-Z0-9\-_.]+?)', [ProjectController::class, 'viewMilestone']);
Router::register('timeline', '/' . PROJECT_SLUG . '/timeline', [TimelineController::class, 'index']);
Router::register('timeline.delete', '/' . PROJECT_SLUG . '/timeline/(?P<eventId>[0-9]+)/delete', [TimelineController::class, 'deleteEvent']);

// Ticket routes
Router::register('tickets.new', '/' . PROJECT_SLUG . '/tickets/new', [TicketController::class, 'create']);
Router::register('tickets.view', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)', [TicketController::class, 'view']);
Router::register('tickets.update', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/update', [TicketController::class, 'update']);
Router::register('tickets.move', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/move', [TicketController::class, 'move']);
Router::register('tickets.delete', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/delete', [TicketController::class, 'delete']);
Router::register('tickets.edit', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/edit', [TicketController::class, 'edit']);
Router::register('tickets.vote', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/vote', [TicketController::class, 'vote']);
Router::register('tickets.voters', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/voters', [TicketController::class, 'voters']);
Router::register('ticket.history.edit', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/history/(?P<id>[0-9]+)/edit', [TicketHistoryController::class, 'edit']);
Router::register('ticket.history.delete', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/history/(?P<id>[0-9]+)/delete', [TicketHistoryController::class, 'delete']);
Router::register('ticket.tasks.toggle', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/tasks/(?P<task_id>[0-9]+)', [TicketTasksController::class, 'toggle']);
Router::register('ticket.tasks.manage', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/tasks/manage', [TicketTasksController::class, 'manage']);
Router::register('tickets.massActions', '/' . PROJECT_SLUG . '/tickets/mass-actions', [TicketController::class, 'massActions']);
Router::register('tickets', '/' . PROJECT_SLUG . '/tickets', [TicketController::class, 'index']);

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
Router::register('project.settings', '/' . PROJECT_SLUG . '/settings', [ProjectSettingsController::class, 'index']);
Router::register('project.settings.milestones', '/' . PROJECT_SLUG . '/settings/milestones', [MilestonesController::class, 'index']);
Router::register('project.settings.milestones.new', '/' . PROJECT_SLUG . '/settings/milestones/new', [MilestonesController::class, 'new']);
Router::register('project.settings.milestones.edit', '/' . PROJECT_SLUG . '/settings/milestones/(?P<id>[0-9]+)/edit', [MilestonesController::class, 'edit']);
Router::register('project.settings.milestones.delete', '/' . PROJECT_SLUG . '/settings/milestones/(?P<id>[0-9]+)/delete', [MilestonesController::class, 'delete']);
Router::register('project.settings.components', '/' . PROJECT_SLUG . '/settings/components', [ComponentsController::class, 'index']);
Router::register('project.settings.components.new', '/' . PROJECT_SLUG . '/settings/components/new', [ComponentsController::class, 'new']);
Router::register('project.settings.components.edit', '/' . PROJECT_SLUG . '/settings/components/(?P<id>[0-9]+)/edit', [ComponentsController::class, 'edit']);
Router::register('project.settings.components.delete', '/' . PROJECT_SLUG . '/settings/components/(?P<id>[0-9]+)/delete', [ComponentsController::class, 'delete']);
Router::register('project.settings.members', '/' . PROJECT_SLUG . '/settings/members', [MembersController::class, 'index']);
Router::register('project.settings.members.new', '/' . PROJECT_SLUG . '/settings/members/new', [MembersController::class, 'new']);
Router::register('project.settings.members.delete', '/' . PROJECT_SLUG . '/settings/members/(?P<id>[0-9]+)/delete', [MembersController::class, 'delete']);
Router::register('project.settings.members.save', '/' . PROJECT_SLUG . '/settings/members/save', [MembersController::class, 'save']);
Router::register('project.settings.customFields.index', '/' . PROJECT_SLUG . '/settings/custom_fields', [CustomFieldsController::class, 'index']);
Router::register('project.settings.customFields.new', '/' . PROJECT_SLUG . '/settings/custom_fields/new', [CustomFieldsController::class, 'new']);
Router::register('project.settings.customFields.edit', '/' . PROJECT_SLUG . '/settings/custom_fields/(?P<id>[0-9]+)/edit', [CustomFieldsController::class, 'edit']);
Router::register('project.settings.customFields.delete', '/' . PROJECT_SLUG . '/settings/custom_fields/(?P<id>[0-9]+)/delete', [CustomFieldsController::class, 'delete']);
Router::register('project.settings.permissions', '/' . PROJECT_SLUG . '/settings/permissions/(?P<type>groups|roles)', [PermissionsController::class, 'index']);

// Subscription routes
Router::register('unsubscribe', '/unsubscribe/(?P<uuid>[\w\-]+)', [SubscriptionsController::class, 'unsubscribe']);
Router::register('project_subunsub', '/' . PROJECT_SLUG . '/(?:un)?subscribe', [SubscriptionsController::class, 'toggleProject']);
Router::register('milestone_subunsub', '/' . PROJECT_SLUG . '/milestone/(?P<milestone_slug>[a-zA-Z0-9\-_.]+?)/(?:un)?subscribe', [SubscriptionsController::class, 'toggleMilestone']);
Router::register('ticket_subunsub', '/' . PROJECT_SLUG . '/tickets/(?P<ticket_id>[0-9]+)/(?:un)?subscribe', [SubscriptionsController::class, 'toggleTicket']);

// ------------------------------------------------
// AdminCP routes
Router::register('admin.dashboard', '/admin', [DashboardController::class, 'index']);
Router::register('admin.settings', '/admin/settings', [AdminSettingsController::class, 'index']);

// Projects
Router::register('admin.projects', '/admin/projects', [ProjectsController::class, 'index']);
Router::register('admin.projects.new', '/admin/projects/new', [ProjectsController::class, 'new']);
Router::register('admin.projects.delete', '/admin/projects/(?P<id>[0-9]+)/delete', [ProjectsController::class, 'delete']);

// Plugins
Router::register('plugins', '/admin/plugins', [Plugins::class, 'index']);
Router::register('plugins.install', '/admin/plugins/(?P<file>[a-zA-Z0-9\-\_]+)/install', [Plugins::class, 'install']);
Router::register('plugins.enable', '/admin/plugins/(?P<file>[a-zA-Z0-9\-\_]+)/enable', [Plugins::class, 'enable']);
Router::register('plugins.disable', '/admin/plugins/(?P<file>[a-zA-Z0-9\-\_]+)/disable', [Plugins::class, 'disable']);
Router::register('plugins.uninstall', '/admin/plugins/(?P<file>[a-zA-Z0-9\-\_]+)/uninstall', [Plugins::class, 'uninstall']);

// Users
Router::register('admin.users', '/admin/users', [AdminUsersController::class, 'index']);
Router::register('admin.users.new', '/admin/users/new', [AdminUsersController::class, 'new']);
Router::register('admin.users.edit', '/admin/users/(?P<id>[0-9]+)/edit', [AdminUsersController::class, 'edit']);
Router::register('admin.users.delete', '/admin/users/(?P<id>[0-9]+)/delete', [AdminUsersController::class, 'delete']);
Router::register('admin.users.mass_actions', '/admin/users/mass_actions', [AdminUsersController::class, 'massActions']);

// User groups
Router::register('admin.groups', '/admin/groups', [GroupsController::class, 'index']);
Router::register('admin.groups.new', '/admin/groups/new', [GroupsController::class, 'new']);
Router::register('admin.groups.edit', '/admin/groups/(?P<id>[0-9]+)/edit', [GroupsController::class, 'edit']);
Router::register('admin.groups.delete', '/admin/groups/(?P<id>[0-9]+)/delete', [GroupsController::class, 'delete']);

// Project roles
Router::register('admin.project.roles', '/admin/roles', [ProjectRolesController::class, 'index']);
Router::register('admin.project.roles.new', '/admin/roles/new', [ProjectRolesController::class, 'new']);
Router::register('admin.project.roles.edit', '/admin/roles/(?P<id>[0-9]+)/edit', [ProjectRolesController::class, 'edit']);
Router::register('admin.project.roles.delete', '/admin/roles/(?P<id>[0-9]+)/delete', [ProjectRolesController::class, 'delete']);

// Ticket types
Router::register('admin.types', '/admin/tickets/types', [TypesController::class, 'index']);
Router::register('admin.types.new', '/admin/tickets/types/new', [TypesController::class, 'new']);
Router::register('admin.types.edit', '/admin/tickets/types/(?P<id>[0-9]+)/edit', [TypesController::class, 'edit']);
Router::register('admin.types.delete', '/admin/tickets/types/(?P<id>[0-9]+)/delete', [TypesController::class, 'delete']);

// Ticket statuses
Router::register('admin.statuses', '/admin/tickets/statuses', [StatusesController::class, 'index']);
Router::register('admin.statuses.new', '/admin/tickets/statuses/new', [StatusesController::class, 'new']);
Router::register('admin.statuses.edit', '/admin/tickets/statuses/(?P<id>[0-9]+)/edit', [StatusesController::class, 'edit']);
Router::register('admin.statuses.delete', '/admin/tickets/statuses/(?P<id>[0-9]+)/delete', [StatusesController::class, 'delete']);

// Severities
Router::register('admin.severities', '/admin/severities', [SeveritiesController::class, 'index']);
Router::register('admin.severities.new', '/admin/severities/new', [SeveritiesController::class, 'new']);
Router::register('admin.severities.edit', '/admin/severities/(?P<id>[0-9]+)/edit', [SeveritiesController::class, 'edit']);
Router::register('admin.severities.delete', '/admin/severities/(?P<id>[0-9]+)/delete', [SeveritiesController::class, 'delete']);

// Priorities
Router::add('/admin/priorities', 'traq::controllers::admin::Priorities.index');
Router::add('/admin/priorities/new', 'traq::controllers::admin::Priorities.new');
Router::add('/admin/priorities/([0-9]+)/(edit|delete)', 'traq::controllers::admin::Priorities.$2/$1');
