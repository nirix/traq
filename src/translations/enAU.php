<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
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

namespace Traq\Translations;

use Traq\Traq;
use Avalon\Language;

/**
 * Australian English translation.
 *
 * @author Jack P.
 * @copyright (C) Jack P.
 * @package Traq\Translations
 */
$enAU = new Language(function($t) {
    $t->name   = "English (Australian)";
    $t->locale = "enAU";

    $t->strings = array(
        'copyright' => "Powered by Traq " . Traq::version() . " &copy; 2009-" . date("Y") . " Traq.io",

        // --------------------------------------------------------------------
        // AdminCP
        'admincp'     => "AdminCP",
        'dashboard'   => "Dashboard",
        'latest_news' => "Latest News",

        // --------------------------------------------------------------------
        // Changelog
        'changelog' => "Changelog",

        // --------------------------------------------------------------------
        // Components
        'components'     => "Components",
        'new_component'  => "New Component",
        'edit_component' => "Edit Component",

        // --------------------------------------------------------------------
        // Custom Fields
        'custom_fields' => "Custom Fields",

        // --------------------------------------------------------------------
        // Confirmations:
        'confirm.yes'    => "Yes",
        'confirm.no'     => "No",
        'confirm.delete' => "Are you sure?",

        // --------------------------------------------------------------------
        // Errors
        'errors.404.title'                    => "I accidentally the whole page...",
        'errors.404.message'                  => "It appears the page '{1}' doesn't exist.",
        'errors.404.modal.title'              => "Oh, it's a popup!",
        'errors.no_permission.title'          => "You shall not pass!",
        'errors.no_permission.message'        => "You don't have permission to access this page.",
        'errors.invalid_username_or_password' => "Invalid username and/or password.",
        'errors.account.activation_required'  => "You must first activate your account.",
        'errors.correct_the_following'        => "Please correct the following issues",

        // --------------------------------------------------------------------
        // Filters
        'filter_events' => "Filter Events",
        'apply_filters' => "Apply Filters",

        // --------------------------------------------------------------------
        // Forms
        'create' => "Create",
        'save'   => "Save",
        'cancel' => "Cancel",
        'close'  => "Close",
        'edit'   => "Edit",
        'delete' => "Delete",

        // --------------------------------------------------------------------
        // Groups
        'groups'     => "Groups",
        'new_group'  => "New Group",
        'edit_group' => "Edit Group",
        'is_admin'   => "Is Admin",

        // --------------------------------------------------------------------
        // Issues
        'issues'            => "Issues",
        'create_issue'      => "Create Issue",
        'open'              => "Open",
        'closed'            => "Closed",
        'total'             => "Total",
        'issue.page-title'  => "#{1} - {2}",
        'issue.page-header' => "#{1} - {2}",

        // Issue properties
        'id'          => "ID",
        'ticket_id'   => "ID",
        'summary'     => "Summary",
        'status'      => "Status",
        'owner'       => "Owner",
        'type'        => "Type",
        'component'   => "Component",
        'milestone'   => "Milestone",
        'assigned_to' => "Assigned to",
        'priority'    => "Priority",
        'severity'    => "Severity",
        'created_at'  => "Created",
        'updated_at'  => "Updated",
        'votes'       => "Votes",
        'created'     => "Created",
        'updated'     => "Updated",

        // --------------------------------------------------------------------
        // Settings
        'settings'                          => "Settings",
        'traq_settings'                     => "Traq Settings",
        'settings.title'                    => "Traq Title",
        'settings.default_language'         => "Default Language",
        'settings.theme'                    => "Theme",
        'settings.site'                     => "Site Settings",
        'settings.site.name'                => "Site Name",
        'settings.site.url'                 => "Site URL",
        'settings.users.allow_registration' => "Allow Registration",
        'settings.users.email_validation'   => "Email Validation",
        'settings.date_and_time'            => "Date and Time",
        'settings.date_time_format'         => "Date Time Format",
        'settings.date_format'              => "Date Format",
        'settings.timeline.day_format'      => "Timeline Day Format",
        'settings.timeline.time_format'     => "Timeline Time Format",
        'settings.notifications.from_email' => "From Email",
        'settings.issues.history_sorting'   => "History Sorting",
        'settings.issues.creation_delay'    => "Creation Delay",

        // --------------------------------------------------------------------
        // Ticket listing
        'filters' => "Filters",
        'columns' => "Columns",
        'update'  => "Update",

        // --------------------------------------------------------------------
        // Milestones
        'milestones'       => "Milestones",
        'new_milestone'    => "New Milestone",
        'edit_milestone'   => "Edit Milestone",
        'delete_milestone' => "Delete Milestone",
        'due_date'         => "Due Date",

        // --------------------------------------------------------------------
        // Misc
        'ascending'               => "Ascending",
        'descending'              => "Descending",
        'x_by_x'                  => "{1} by {2}",
        'information'             => "Information",
        'oldest_first'            => "Oldest First",
        'newest_first'            => "Newest First",
        'notifications'           => "Notifications",
        'leave_blank_for_current' => "Leave blank for current",

        // --------------------------------------------------------------------
        // Pagination
        'next'     => "Next",
        'previous' => "Previous",

        // --------------------------------------------------------------------
        // Plugins
        'plugins'   => "Plugins",
        'authors'   => "Authors",
        'version'   => "Version",
        'install'   => "Install",
        'uninstall' => "Uninstall",
        'enable'    => "Enable",
        'disable'   => "Disable",

        // --------------------------------------------------------------------
        // Priorities
        'priorities'    => "Priorities",
        'new_priority'  => "New Priority",
        'edit_priority' => "Edit Priority",

        // --------------------------------------------------------------------
        // Projects
        'projects'               => "Projects",
        'project'                => "Project",
        'new_project'            => "New Project",
        'edit_project'           => "Edit Project",
        'name'                   => "Name",
        'slug'                   => "Slug",
        'codename'               => "Codename",
        'description'            => "Description",
        'enable_wiki'            => "Enable Wiki",
        'display_order'          => "Display Order",
        'default_ticket_type'    => "Default ticket type",
        'default_ticket_sorting' => "Default ticket sorting",

        // Project Settings
        'project_settings' => "Project Settings",
        'members'          => "Members",

        // --------------------------------------------------------------------
        // Project Roles
        'role'       => "Role",
        'roles'      => "Roles",
        'new_role'   => "New Role",
        'edit_role'  => "Edit Role",
        'assignable' => "Assignable",

        // --------------------------------------------------------------------
        // Roadmap
        'roadmap'   => "Roadmap",
        'all'       => "All",
        'active'    => "Active",
        'completed' => "Completed",
        'cancelled' => "Cancelled",
        'x_open'    => "{1} open",
        'x_started' => "{1} started",
        'x_closed'  => "{1} closed",

        // --------------------------------------------------------------------
        // Severities
        'severities'    => "Severities",
        'new_severity'  => "New Severity",
        'edit_severity' => "Edit Severity",

        // --------------------------------------------------------------------
        // Statuses
        'statuses'          => "Statuses",
        'status.type.0'     => "Closed",
        'status.type.1'     => "Open",
        'status.type.2'     => "Started",
        'new_status'        => "New Status",
        'edit_status'       => "Edit Status",
        'show_on_changelog' => "Show on Changelog",

        // --------------------------------------------------------------------
        // Timeline
        'timeline'                     => "Timeline",
        'activity'                     => "Activity",
        'metrics'                      => "Metrics",
        'timeline.ticket_created'      => "Created {type} #{id}: {summary}",
        'timeline.ticket_closed'       => "Closed {type} #{id} as {status}: {summary}",
        'timeline.ticket_reopened'     => "Reopened {type} #{id} as {status}: {summary}",
        'timeline.ticket_updated'      => "Updated {type} #{id}: {summary}",
        'timeline.ticket_comment'      => "Commented on {link}",
        'timeline.milestone_completed' => "Milestone {name} completed",
        'timeline.milestone_cancelled' => "Milestone {name} cancelled",
        'timeline.ticket_moved_from'   => "Moved issue ({issue}) from {project}",
        'timeline.ticket_moved_to'     => "Moved issue ({issue}) to {project}",
        'timeline.wiki_page_created'   => "Created {title} wiki page",
        'timeline.wiki_page_edited'    => "Edited {title} wiki page",
        'timeline.by_x'                => "by {1}",

        // --------------------------------------------------------------------
        // Timeline filters
        'timeline.filters.new_tickets'           => "New tickets",
        'timeline.filters.tickets_opened_closed' => "Tickets open/closed",
        'timeline.filters.ticket_updates'        => "Ticket updates",
        'timeline.filters.ticket_comments'       => "Ticket comments",
        'timeline.filters.ticket_moves'          => "Ticket migrations",
        'timeline.filters.milestones'            => "Milestones",
        'timeline.filters.wiki_pages'            => "Wiki pages",

        // --------------------------------------------------------------------
        // Types
        'types'     => "Types",
        'new_type'  => "New Type",
        'edit_type' => "Edit Type",
        'bullet'    => "Bullet",
        'template'  => "Template",

        // --------------------------------------------------------------------
        // Users
        'users'     => "Users",
        'new_user'  => "New User",
        'edit_user' => "Edit User",
        'newest'    => "Newest",
        'profile'   => "Profile",
        'usercp'    => "UserCP",
        'register'  => "Register",
        'login'     => "Login",
        'logout'    => "Logout",
        'username'  => "Username",
        'password'  => "Password",
        'email'     => "Email",

        // --------------------------------------------------------------------
        // Wiki
        'wiki'        => "Wiki",
        'home'        => "Home",
        'pages'       => "Pages",
        'new_page'    => "New Page",
        'edit_page'   => "Edit Page",
        'delete_page' => "Delete Page",
        'revisions'   => "Revisions",
        'revision_x'  => "Revision {1}",
        'title'       => "Title",
        'content'     => "Content"
    );
});
