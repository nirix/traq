<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

namespace Traq\Translations;

use Traq\Kernel;
use Avalon\Language;

/**
 * Australian English translation.
 *
 * @author Jack P.
 * @copyright (C) Jack P.
 * @package Traq\Translations
 */
$english = new Language(function ($t) {
    $t->name   = 'English';
    $t->locale = 'en_AU';

    $t->strings = array(
        // --------------------------------------------------------------------
        // AdminCP
        'admincp'     => 'AdminCP',
        'dashboard'   => 'Dashboard',
        'latest_news' => 'Latest News',

        // --------------------------------------------------------------------
        // Changelog
        'changelog' => 'Changelog',

        // --------------------------------------------------------------------
        // Components
        'components'     => 'Components',
        'new_component'  => 'New Component',
        'edit_component' => 'Edit Component',

        // --------------------------------------------------------------------
        // Confirmations
        'confirm.delete_x' => 'Are you sure you want to delete \'{0}\' ?',

        // --------------------------------------------------------------------
        // Custom Fields
        // 'custom_fields'     => 'Custom Fields',
        // 'new_field'         => 'New Field',
        // 'new_custom_field'  => 'New Custom Field',
        // 'edit_custom_field' => 'Edit Custom Field',
        // 'required'          => 'Required',
        // 'min_length'        => 'Min. length',
        // 'max_length'        => 'Max. length',
        // 'regex'             => 'Regex',
        // 'default_value'     => 'Default value',
        // 'multichoice'       => 'Multichoice',
        // 'values'            => 'Values',
        // 'ticket_types'      => 'Ticket types',

        // --------------------------------------------------------------------
        // Errors
        // 'errors.404.title'                      => 'I accidentally the whole page...',
        // 'errors.404.message'                    => "It appears the page '{0}' doesn't exist.",
        // 'errors.404.modal.title'                => "Oh, it's a popup!",
        // 'errors.403.title'                      => 'You shall not pass!',
        // 'errors.403.message'                    => "You don't have permission to access this page.",
        'errors.invalid_username_or_password'   => "Invalid username and/or password.",
        // 'errors.incorrect_password'             => 'Incorrect password',
        // 'errors.account.activation_required'    => 'You must first activate your account.',
        'errors.correct_the_following'          => 'Please correct the following issues',
        // 'errors.users.already_a_project_member' => 'User is already a member',

        // --------------------------------------------------------------------
        // Filters
        'filter_events' => 'Filter Events',
        'apply_filters' => 'Apply Filters',

        // --------------------------------------------------------------------
        // Groups
        'groups'     => 'Groups',
        'new_group'  => 'New Group',
        'edit_group' => 'Edit Group',
        'is_admin'   => 'Is Admin',

        // --------------------------------------------------------------------
        // Tickets
        'tickets'            => 'Tickets',
        // 'new_ticket'         => 'New Ticket',
        // 'create_ticket'      => 'Create Ticket',
        'open'               => 'Open',
        'closed'             => 'Closed',
        'total'              => 'Total',
        'ticket.page-title'  => "#{0} - {1}",
        'ticket.page-header' => "#{0} - {1}",
        // 'edit_ticket_description' => 'Edit Ticket Description',

        // Ticket properties
        // 'ticket_properties' => 'Issue Properties',
        'id'              => 'ID',
        'ticket_id'       => 'ID',
        'summary'         => 'Summary',
        'body'            => 'Description',
        'status'          => 'Status',
        'owner'           => 'Owner',
        'type'            => 'Type',
        'component'       => 'Component',
        'milestone'       => 'Milestone',
        'reported_by'     => 'Reported by',
        'assigned_to'     => 'Assigned to',
        'priority'        => 'Priority',
        'severity'        => 'Severity',
        'created_at'      => 'Created',
        'updated_at'      => 'Updated',
        'votes'           => 'Votes',
        'related_tickets' => 'Related Tickets',
        'created'         => 'Created',
        'updated'         => 'Updated',

        // Ticket history
        'ticket_history' => 'Ticket History',
        // Most fields
        'ticket_history.x_from_x_to_x' => 'Changed {0} from {1} to {2}',
        'ticket_history.x_from_null_to_x' => "Set {0} to {2}",
        'ticket_history.x_from_x_to_null' => "Cleared {0}, was {1}",
        // Assignee field
        'ticket_history.assignee_from_x_to_x'    => "Reassigned issue from {1} to {2}",
        'ticket_history.assignee_from_null_to_x' => "Assigned issue to {2}",
        'ticket_history.assignee_from_x_to_null' => "Unassigned issue from {1}",
        // Actions
        'ticket_history.close'          => "Closed issue as {1}",
        'ticket_history.reopen'         => "Reopened issue as {1}",
        'ticket_history.add_attachment' => "Added attachment {1}",

        // Update ticket
        'update_ticket' => 'Update Ticket',
        'comment'       => 'Comment',

        // Filters
        'is'             => 'is',
        'or'             => 'or',
        'is_not'         => 'is not',
        'contains'       => 'Contains',
        'doesnt_contain' => 'Doesn\'t contain',

        // --------------------------------------------------------------------
        // Notifications
        // 'notifications.hello_x' => 'Hello {name}',

        // Account Activation
        // 'notifications.account_activation.subject'  => '{title} Account Activation',
        // 'notifications.account_activation.body.txt' => 'Someone recently created an account at {title} with this ' .
        //                                                 "email address, if this wasn't you, feel free to ignore this ' .
        //                                                 'email, otherwise to activate your account please visit the ' .
        //                                                 'URL below.",

        // --------------------------------------------------------------------
        // Settings
        'settings'                          => 'Settings',
        'traq_settings'                     => 'Traq Settings',
        'settings.title'                    => 'Traq Title',
        'settings.default_language'         => 'Default Language',
        'settings.theme'                    => 'Theme',
        'settings.site'                     => 'Site Settings',
        'settings.site.name'                => 'Site Name',
        'settings.site.url'                 => 'Site URL',
        'settings.users.allow_registration' => 'Allow Registration',
        'settings.users.email_validation'   => 'Email Validation',
        'settings.date_and_time'            => 'Date and Time',
        'settings.date_time_format'         => 'Date Time Format',
        'settings.date_format'              => 'Date Format',
        'settings.timeline.day_format'      => 'Timeline Day Format',
        'settings.timeline.time_format'     => 'Timeline Time Format',
        'settings.notifications.from_email' => 'From Email',
        'settings.tickets.history_sorting'  => 'History Sorting',
        'settings.tickets.creation_delay'   => 'Creation Delay',

        // --------------------------------------------------------------------
        // Ticket listing
        'filters' => 'Filters',
        'columns' => 'Columns',
        'update'  => 'Update',

        // --------------------------------------------------------------------
        // Milestones
        'milestones'       => 'Milestones',
        'new_milestone'    => 'New Milestone',
        'edit_milestone'   => 'Edit Milestone',
        // 'delete_milestone' => 'Delete Milestone',
        'due_date'         => 'Due Date',

        // --------------------------------------------------------------------
        // Misc
        // 'add'                     => 'Add',
        // 'renew'                   => 'Renew',
        // 'ascending'               => 'Ascending',
        // 'descending'              => 'Descending',
        'x_by_x'                  => '{0} by {1}',
        // 'information'             => 'Information',
        'oldest_first'            => 'Oldest First',
        'newest_first'            => 'Newest First',
        'notifications'           => 'Notifications',
        'level'                   => 'Level',
        'leave_blank_for_current' => 'Leave blank for current',
        // 'never'                   => 'Never',
        // 'none'                    => 'None',
        // 'no_one'                  => 'No one',
        'toggle_navigation'       => 'Toggle Navigation',

        // --------------------------------------------------------------------
        // Plugins
        'plugins'   => 'Plugins',
        'authors'   => 'Authors',
        'version'   => 'Version',
        'install'   => 'Install',
        'uninstall' => 'Uninstall',
        'enable'    => 'Enable',
        'disable'   => 'Disable',

        // --------------------------------------------------------------------
        // Priorities
        'priorities'    => 'Priorities',
        'new_priority'  => 'New Priority',
        'edit_priority' => 'Edit Priority',

        // --------------------------------------------------------------------
        // Projects
        'projects'               => 'Projects',
        'project'                => 'Project',
        'new_project'            => 'New Project',
        'edit_project'           => 'Edit Project',
        'name'                   => 'Name',
        'slug'                   => 'Slug',
        'codename'               => 'Codename',
        'description'            => 'Description',
        'enable_wiki'            => 'Enable Wiki',
        'display_order'          => 'Display Order',
        'default_ticket_type'    => 'Default ticket type',
        'default_ticket_sorting' => 'Default ticket sorting',

        // Project Settings
        // 'project_settings' => 'Project Settings',
        // 'members'          => 'Members',

        // --------------------------------------------------------------------
        // Project Roles
        'project_roles' => 'Project Roles',
        // 'role'          => 'Role',
        'roles'         => 'Roles',
        'new_role'      => 'New Role',
        'edit_role'     => 'Edit Role',
        'assignable'    => 'Assignable',

        // --------------------------------------------------------------------
        // Roadmap
        'roadmap'   => 'Roadmap',
        'all'       => 'All',
        'active'    => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'x_open'    => '{0} open',
        'x_started' => '{0} started',
        'x_closed'  => '{0} closed',

        // --------------------------------------------------------------------
        // Severities
        'severities'    => 'Severities',
        'new_severity'  => 'New Severity',
        'edit_severity' => 'Edit Severity',

        // --------------------------------------------------------------------
        // Statuses
        'statuses'          => 'Statuses',
        'status.type.0'     => 'Closed',
        'status.type.1'     => 'Open',
        'status.type.2'     => 'Started',
        'new_status'        => 'New Status',
        'edit_status'       => 'Edit Status',
        'show_on_changelog' => 'Show on Changelog',

        // --------------------------------------------------------------------
        // Timeline
        'timeline'                     => 'Timeline',
        'activity'                     => 'Activity',
        'metrics'                      => 'Metrics',
        'timeline.ticket_created'      => "Created {type} #{id}: {summary}",
        'timeline.ticket_closed'       => "Closed {type} #{id} as {status}: {summary}",
        'timeline.ticket_reopened'     => "Reopened {type} #{id} as {status}: {summary}",
        'timeline.ticket_updated'      => "Updated {type} #{id}: {summary}",
        'timeline.ticket_comment'      => 'Commented on {link}',
        'timeline.milestone_completed' => 'Milestone {name} completed',
        'timeline.milestone_cancelled' => 'Milestone {name} cancelled',
        'timeline.ticket_moved_from'   => 'Moved issue ({issue}) from {project}',
        'timeline.ticket_moved_to'     => 'Moved issue ({issue}) to {project}',
        'timeline.wiki_page_created'   => 'Created {title} wiki page',
        'timeline.wiki_page_edited'    => 'Edited {title} wiki page',
        'timeline.by_x'                => 'by {0}',

        // --------------------------------------------------------------------
        // Timeline filters
        'timeline.filters.new_tickets'           => 'New tickets',
        'timeline.filters.tickets_opened_closed' => "Tickets open/closed",
        'timeline.filters.ticket_updates'        => 'Ticket updates',
        'timeline.filters.ticket_comments'       => 'Ticket comments',
        'timeline.filters.ticket_moves'          => 'Ticket migrations',
        'timeline.filters.milestones'            => 'Milestones',
        'timeline.filters.wiki_pages'            => 'Wiki pages',

        // --------------------------------------------------------------------
        // Types
        'types'     => 'Types',
        'new_type'  => 'New Type',
        'edit_type' => 'Edit Type',
        'bullet'    => 'Bullet',
        'template'  => 'Template',

        // --------------------------------------------------------------------
        // Users
        'users'          => 'Users',
        'new_user'       => 'New User',
        'edit_user'      => 'Edit User',
        'newest'         => 'Newest',
        // 'profile'        => 'Profile',
        // 'usercp'         => 'UserCP',
        'register'       => 'Register',
        'login'          => 'Login',
        'logout'         => 'Logout',
        'username'       => 'Username',
        'password'       => 'Password',
        'email'          => 'Email',
        'group'          => 'Group',
        // 'create_account' => 'Create Account',

        // UserCP
        // 'options'          => 'Options',
        // 'api_key'          => 'API Key',
        // 'current_password' => 'Current Password',
        // 'language'         => 'Language',
        // 'subscriptions'    => 'Subscriptions',


        // --------------------------------------------------------------------
        // Wiki
        'wiki'        => 'Wiki',
        'home'        => 'Home',
        'pages'       => 'Pages',
        'new_page'    => 'New Page',
        'revisions_for_x' => 'Revisions for {0}',
        'edit_page'   => 'Edit Page',
        // 'delete_page' => 'Delete Page',
        'revisions'   => 'Revisions',
        'revision_x'  => 'Revision {0}',
        'title'       => 'Title',
        'content'     => 'Content',

        // --------------------------------------------------------------------
        // Permissions
        'permissions' => 'Permissions',
        'default'     => 'Default',

        // Projects
        'permissions.view'                                     => 'View project',
        'permissions.project_settings'                         => 'Project Settings',
        'permissions.delete_timeline_events'                   => 'Delete timeline events',

        // Tickets
        'permissions.view_tickets'                             => 'View tickets',
        'permissions.create_tickets'                           => 'Create tickets',
        'permissions.update_tickets'                           => 'Update tickets',
        'permissions.delete_tickets'                           => 'Delete tickets',
        'permissions.move_tickets'                             => 'Move tickets',
        'permissions.comment_on_tickets'                       => 'Comment on tickets',
        'permissions.edit_ticket_description'                  => 'Edit ticket description',
        'permissions.vote_on_tickets'                          => 'Vote on tickets',
        'permissions.add_attachments'                          => 'Add attachments',
        'permissions.view_attachments'                         => 'View attachments',
        'permissions.delete_attachments'                       => 'Delete attachments',
        'permissions.perform_mass_actions'                     => 'Perform mass actions',

        // Ticket properties
        'permissions.ticket_properties_set_assigned_to'        => 'Set assigned to',
        'permissions.ticket_properties_set_milestone'          => 'Set milestone',
        'permissions.ticket_properties_set_version'            => 'Set version',
        'permissions.ticket_properties_set_component'          => 'Set component',
        'permissions.ticket_properties_set_severity'           => 'Set severity',
        'permissions.ticket_properties_set_priority'           => 'Set priority',
        'permissions.ticket_properties_set_status'             => 'Set status',
        'permissions.ticket_properties_set_tasks'              => 'Set tasks',
        'permissions.ticket_properties_set_related_tickets'    => 'Set related_tickets',
        'permissions.ticket_properties_change_type'            => 'Change type',
        'permissions.ticket_properties_change_assigned_to'     => 'Change assigned to',
        'permissions.ticket_properties_change_milestone'       => 'Change milestone',
        'permissions.ticket_properties_change_version'         => 'Change version',
        'permissions.ticket_properties_change_component'       => 'Change component',
        'permissions.ticket_properties_change_severity'        => 'Change severity',
        'permissions.ticket_properties_change_priority'        => 'Change priority',
        'permissions.ticket_properties_change_status'          => 'Change status',
        'permissions.ticket_properties_change_summary'         => 'Change summary',
        'permissions.ticket_properties_change_tasks'           => 'Change tasks',
        'permissions.ticket_properties_change_related_tickets' => 'Change related tickets',
        'permissions.ticket_properties_complete_tasks'         => 'Complete tasks',

        // Ticket history
        'permissions.edit_ticket_history'                      => 'Edit ticket history',
        'permissions.delete_ticket_history'                    => 'Delete ticket history',

        // Wiki
        'permissions.create_wiki_page'                         => 'Create wiki page',
        'permissions.edit_wiki_page'                           => 'Edit wiki page',
        'permissions.delete_wiki_page'                         => 'Delete wiki page'
    );
});
