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

namespace traq\locale;

/**
 * enUS localization class.
 *
 * @author Jack P.
 * @copyright (C) Jack P.
 * @package Traq
 * @subpackage Locale
 */
class enUS extends \traq\libraries\Locale
{
    protected static $info = array(
        'name'    => "English",
        'author'  => "Jack Polgar",
        'version' => "3.0",

        // Locale information
        'language'       => "English",
        'language_short' => "en",
        'locale'         => "US"
    );

    public static function locale()
    {
        return array(
            'traq'             => "Traq",
            'copyright'        => "Powered by Traq " . TRAQ_VER . " &copy; 2009-" . date("Y") . " Traq.io",
            'projects'         => "Projects",
            'project_info'     => "Project Info",
            'tickets'          => "Tickets",
            'roadmap'          => "Roadmap",
            'settings'         => "Settings",
            'information'      => "Information",
            'milestones'       => "Milestones",
            'components'       => "Components",
            'project_settings' => "Project Settings",
            'name'             => "Name",
            'slug'             => "Slug",
            'codename'         => "Codename",
            'open'             => "Open",
            'closed'           => "Closed",
            'cancel'           => "Cancel",
            'new'              => "New",
            'wiki'             => "Wiki",
            'x_open'           => "{1} open",
            'x_closed'         => "{1} closed",
            'yes'              => "Yes",
            'no'               => "No",
            'created'          => "Created",
            'updated'          => "Updated",
            'project'          => "Project",
            'never'            => "Never",
            'votes'            => "Votes",
            'update_ticket'    => "Update Ticket",
            'comment'          => "Comment",
            'update'           => "Update",
            'x_by_x'           => "{1} by {2}",
            'submit'           => "Submit",
            'see_all'          => "See all",
            'close'            => "Close",
            'all'              => "All",
            'active'           => "Active",
            'completed'        => "Completed",
            'cancelled'        => "Canceled",
            'due_x'            => "Due {1}",
            'members'          => "Members",
            'none'             => "None",
            'member_since'     => "Member since",
            'unknown'          => "Unknown",
            'changelog'        => "Changelog",
            'or'               => "or",
            'language'         => "Language",
            'filters'          => "Filters",
            'is'               => "is",
            'is_not'           => "is not",
            'contains'         => "contains",
            'does_not_contain' => "does not contain",
            'subscribe'        => "Subscribe",
            'unsubscribe'      => "Unsubscribe",
            'for'              => "For",

            // AdminCP
            'admin.theme_select_option' => "{1} (v{2} by {3})",
            'traq_settings'        => "Traq Settings",
            'users'                => "Users",
            'groups'               => "Groups",
            'new_project'          => "New Project",
            'plugins'              => "Plugins",
            'enabled_plugins'      => "Enabled Plugins",
            'disabled_plugins'     => "Disabled Plugins",
            'author'               => "Author",
            'version'              => "Version",
            'enable'               => "Enable",
            'disable'              => "Disable",
            'new_user'             => "New User",
            'edit_user'            => "Edit User",
            'group'                => "Group",
            'new_group'            => "New Group",
            'edit_group'           => "Edit Group",
            'types'                => "Types",
            'statuses'             => "Statuses",
            'new_type'             => "New Type",
            'edit_type'            => "Edit Type",
            'bullet'               => "Bullet",
            'show_on_changelog'    => "Show on Changelog",
            'template'             => "Template",
            'new_status'           => "New Status",
            'edit_status'          => "Edit Status",
            'traq_title'           => "Traq Title",
            'default_language'     => "Default Language",
            'theme'                => "Theme",
            'allow_registration'   => "Allow Registration",
            'date_and_time'        => "Date and Time",
            'date_time_format'     => "Date/Time Format",
            'date_format'          => "Date Format",
            'timeline_day_format'  => "Timeline Day Format",
            'timeline_time_format' => "Timeline Time Format",
            'install'              => "Install",
            'uninstall'            => "Uninstall",
            'roles'                => "Roles",
            'new_role'             => "New Role",
            'edit_role'            => "Edit Role",
            'due'                  => "Due",
            'role'                 => "Role",
            'add'                  => "Add",
            'enable_wiki'          => "Enable Wiki",
            'priorities'           => "Priorities",
            'new_priority'         => "New Priority",
            'edit_priority'        => "Edit Priority",
            'severities'           => "Severities",
            'new_severity'         => "New Severity",
            'edit_severity'        => "Edit Severity",
            'notifications'        => "Notifications",
            'notifications_from_email' => "From Email",

            // Project settings
            'new_milestone'    => "New Milestone",
            'delete_milestone' => "Delete Milestone",
            'edit_milestone'   => "Edit Milestone",
            'new_component'    => "New Component",
            'edit_component'   => "Edit Component",
            'display_order'    => "Display Order",

            // Tickets
            'ticket'                => "Ticket",
            'new_ticket'            => "New Ticket",
            'summary'               => "Summary",
            'status'                => "Status",
            'owner'                 => "Owner",
            'type'                  => "Type",
            'component'             => "Component",
            'milestone'             => "Milestone",
            'description'           => "Description",
            'updates'               => "Updates",
            'severity'              => "Severity",
            'assigned_to'           => "Assigned to",
            'reported'              => "Reported",
            'priority'              => "Priority",
            'edit_ticket'           => "Edit Ticket",
            'no_votes'              => "No votes",
            'attachment'            => "Attachment",
            'attachments'           => "Attachments",
            'edit_ticket_history'   => "Edit Ticket History",
            'x_uploaded_by_x_x_ago' => "{1} uploaded by {2}, {3}",
            'people_who_have_voted_on_this_ticket' => "People who have voted on this ticket ({1})",

            // Users
            'login'                => "Login",
            'logout'               => "Logout",
            'usercp'               => "UserCP",
            'admincp'              => "AdminCP",
            'register'             => "Register",
            'username'             => "Username",
            'password'             => "Password",
            'old_password'         => "Old Password",
            'new_password'         => "New Password",
            'confirm_password'     => "Confirm Password",
            'email'                => "Email",
            'xs_profile'           => "{1}'s Profile",
            'assigned_tickets'     => "Assigned tickets",
            'tickets_created'      => "Tickets created",
            'ticket_updates'       => "Ticket updates",
            'information'          => "Information",
            'options'              => "Options",
            'watch_my_new_tickets' => "Watch my new tickets",
            'subscriptions'        => "Subscriptions",

            // Wiki
            'home'         => "Home",
            'pages'        => "Pages",
            'new_page'     => "New Page",
            'edit_page'    => "Edit Page",
            'delete_page'  => "Delete Page",
            'page_title'   => "Page Title",
            'page_content' => "Page Content",

            // Other
            'actions' => "Actions",
            'create'  => "Create",
            'save'    => "Save",
            'edit'    => "Edit",
            'delete'  => "Delete",

            // Permissions
            'group_permissions' => "Group Permissions",
            'role_permissions'  => "Role Permissions",
            'action'            => "Action",
            'defaults'          => "Defaults",
            'allow'             => "Allow",
            'deny'              => "Deny",
            'permissions' => array(
                // Projects
                'view' => "View",
                'project_settings' => "Project Settings",

                // Tickets
                'tickets' => array(
                    'create_tickets'            => "Create",
                    'update_tickets'            => "Update",
                    'delete_tickets'            => "Delete",
                    'vote_on_tickets'           => "Vote",
                    'comment_on_tickets'        => "Comment",
                    'edit_ticket_description'   => "Edit description",
                    'set_all_ticket_properties' => "Set all properties",
                    'add_attachments'           => "Add attachments",
                    'view_attachments'          => "View attachments",
                    'delete_attachments'        => "Delete attachments",

                    // Ticket History
                    'edit_ticket_history'   => "Edit history",
                    'delete_ticket_history' => "Delete history",
                ),

                // Wiki
                'wiki' => array(
                    'create_wiki_page' => "Create page",
                    'edit_wiki_page'   => "Edit page",
                    'delete_wiki_page' => "Delete page"
                )
            ),

            // Time
            'time'          => "Time",
            'time.ago'      => "{1} ago",
            'time.from_now' => "{1} from now",
            'time.x_and_x'  => "{1} and {2}",
            'time.x_second' => "{1} {plural:{1}, {second|seconds}}",
            'time.x_minute' => "{1} {plural:{1}, {minute|minutes}}",
            'time.x_hour'   => "{1} {plural:{1}, {hour|hours}}",
            'time.x_day'    => "{1} {plural:{1}, {day|days}}",
            'time.x_week'   => "{1} {plural:{1}, {week|weeks}}",
            'time.x_month'  => "{1} {plural:{1}, {month|months}}",
            'time.x_year'   => "{1} {plural:{1}, {year|years}}",

            // Timeline
            'timeline'                     => "Timeline",
            'timeline.ticket_created'      => "{3} #{2} ({1}) created",
            'timeline.ticket_closed'       => "{3} #{2} ({1}) closed as {4}",
            'timeline.ticket_reopened'     => "{3} #{2} ({1}) reopened as {4}",
            'timeline.ticket_comment'      => "Commented on ticket {1}",
            'timeline.milestone_completed' => "Milestone {1} completed",
            'timeline.milestone_cancelled' => "Milestone {1} cancelled",
            'timeline.by_x'                => "by {1}",

            // Help
            'help.slug'               => "A lower case alpha-numerical string with the exception of dashes, underscores and periods to be used in the URL.",
            'help.ticket_type_bullet' => "The bullet style used on the changelog list.",

            // Confirmations
            'confirm.delete'   => "Are you sure you want to delete that?",
            'confirm.delete_x' => "Are you sure you want to delete '{1}' ?",
            'confirm.remove_x' => "Are you sure you want to remove '{1}' ?",

            // Editor
            'editor' => array(
                // Intentionally left empty to use the default
                // strings from the editor.
                //
                // Enter your localisation strings here.
                // example:
                // 'h2' => "My custom string",
                // 'h3' => "Another custom string",
                // and so on...
            ),

            // Ticket history
            'ticket_history' => array(
                'Ticket History',

                // Most fields
                'x_from_x_to_x'    => "Changed {1} from {2} to {3}",
                'x_from_null_to_x' => "Set {1} to {3}",
                'x_from_x_to_null' => "Cleared {1}, was {2}",

                // Assignee field
                'assignee_from_x_to_x'    => "Reassigned ticket from {2} to {3}",
                'assignee_from_null_to_x' => "Assigned ticket to {3}",
                'assignee_from_x_to_null' => "Unassigned ticket from {2}",

                // Actions
                'close'          => "Closed ticket as {2}",
                'reopen'         => "Reopened ticket as {2}",
                'add_attachment' => "Added attachment {2}",
            ),

            // Warnings
            'warnings' => array(
                'delete_milestone' => "Select which milestone to move tickets to."
            ),

            // Errors
            'errors' => array(
                'invalid_username_or_password' => "Invalid Username or Password.",
                'name_blank'                   => "Name cannot be blank",
                'slug_blank'                   => "Slug cannot be blank",
                'slug_in_use'                  => "That slug is already in use",
                'page_title_blank'             => "Page Title cannot be blank",
                'already_voted'                => "You have already voted.",
                'must_be_logged_in'            => "You must be logged in to do that.",

                // 404 error page
                '404' => array(
                    'title'   => "He's dead, Jim!",
                    'message' => "The requested page '{1}' couldn't be found."
                ),

                // No Permission page
                'no_permission' => array(
                    'title'   => "Move along, move along",
                    'message' => "You don't have permission to access this page."
                ),

                // Tickets
                'tickets' => array(
                    'summary_blank'     => "Summary cannot be blank",
                    'description_blank' => "Description cannot be blank"
                ),

                // Ticket types
                'ticket_type.bullet_blank' => "Bullet cannot be blank",

                // User errors
                'users' => array(
                    'username_blank'           => "Username cannot be blank",
                    'name_blank'               => "Name cannot be blank",
                    'username_in_use'          => "That username is already registered",
                    'password_blank'           => "Password cannot be blank",
                    'new_password_blank'       => "Your new password cannot be blank",
                    'confirm_password_blank'   => "You must confirm your password",
                    'invalid_confirm_password' => "Your confirmation password doesn't match your new password",
                    'invalid_password'         => "Invalid password",
                    'email_invalid'            => "Invalid email address",
                    'doesnt_exist'             => "User doesn't exist",
                    'already_a_project_member' => "User is already a project member",
                    'password_same'            => "Your new password cannot be the same as your current password",
                    'username_too_long'        => "Username cannot be longer than 25 characters"
                ),

                // Traq Settings errors
                'settings' => array(
                    'title_blank'              => "Traq Title cannot be blank",
                    'locale_blank'             => "You must select a default language",
                    'theme_blank'              => "You must select a theme",
                    'allow_registration_blank' => "Allow Registration must be set"
                )
            ),

            // ----------------------------------------------------------------------------------------------------
            // Notifications

            // Ticket assigned
            'notifications.ticket_assigned.subject' => "Ticket #{2} on project {4} has been assigned to you",
            'notifications.ticket_assigned.message' => "{2},<br /><br />".
                                                       "Ticket #{3} (<a href=\"{8}\">{4}</a>) on project {6} has been assigned to you.<br /><br />".
                                                       "----------------------------------------------------------------<br />".
                                                       "{5}".
                                                       "----------------------------------------------------------------",

            // Ticket created
            'notifications.ticket_created.subject' => "New ticket #{2} ({3}) on project {4}",
            'notifications.ticket_created.message' => "{2},<br /><br />".
                                                      "Ticket #{3} (<a href=\"{8}\">{4}</a>) has been created on project {6}.<br /><br />".
                                                      "----------------------------------------------------------------<br />".
                                                      "{5}".
                                                      "----------------------------------------------------------------",

            // Ticket updated
            'notifications.ticket_updated.subject' => "Ticket #{2} ({3}) updated on project {4}",
            'notifications.ticket_updated.message' => "{2},<br /><br />".
                                                      "Ticket #{3} (<a href=\"{8}\">{4}</a>) has been updated on project {6}.",

            // Ticket closed
            'notifications.ticket_closed.subject' => "Ticket #{2} ({3}) closed on project {4}",
            'notifications.ticket_closed.message' => "{2},<br /><br />".
                                                     "Ticket #{3} (<a href=\"{8}\">{4}</a>) has been closed on project {6}.",

            // Ticket reopened
            'notifications.ticket_reopened.subject' => "Ticket #{2} ({3}) reopened on project {4}",
            'notifications.ticket_reopened.message' => "{2},<br /><br />".
                                                       "Ticket #{3} (<a href=\"{8}\">{4}</a>) has been reopened on project {6}.",

            // ----------------------------------------------------------------------------------------------------

            // Testing purposes only...
            'test' => array(
                'plurals' => "There {plural:{1}, {is {1} bottle|are {1} bottles}} of scotch on the shelf."
            )
        );
    }
}
