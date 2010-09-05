<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
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
 *
 * $Id$
 */

/**
 * Traq English Localization
 * Name: English
 * Author: Jack Polgar
 */

// Global / Misc / In more than one place
$lang['traq'] = 'Traq';
$lang['poweredby'] = 'Powered by Traq '.TRAQVER.'<br />Copyright &copy; '.date("Y").' Jack Polgar';
$lang['login'] = 'Login';
$lang['register'] = 'Register';
$lang['logout'] = 'Logout';
$lang['usercp'] = 'UserCP';
$lang['admincp'] = 'AdminCP';
$lang['projects'] = 'Projects';
$lang['project_info'] = 'Project Info';
$lang['roadmap'] = 'Roadmap';
$lang['timeline'] = 'Timeline';
$lang['tickets'] = 'Tickets';
$lang['changelog'] = 'Changelog';
$lang['milestone'] = 'Milestone';
$lang['new_ticket'] = 'New Ticket';
$lang['summary'] = 'Summary';
$lang['description'] = 'Description';
$lang['properties'] = 'Properties';
$lang['type'] = 'Type';
$lang['assign_to'] = 'Assign to';
$lang['priority'] = 'Priority';
$lang['severity'] = 'Severity';
$lang['version'] = 'Version';
$lang['component'] = 'Component';
$lang['your_name'] = 'Your name';
$lang['recaptcha'] = 'reCAPTCHA';
$lang['private_ticket'] = 'Private Ticket';
$lang['ticket'] = 'Ticket';
$lang['status'] = 'Status';
$lang['owner'] = 'Owner';
$lang['reported_by'] = 'Reported by';
$lang['assigned_to'] = 'Assigned to';
$lang['attachments'] = 'Attachments';
$lang['remember'] = 'Remember';
$lang['cancel'] = 'Cancel';
$lang['username'] = 'Username';
$lang['password'] = 'Password';
$lang['full_name'] = 'Full name';
$lang['confirm'] = 'Confirm';
$lang['email'] = 'Email';
$lang['settings'] = 'Settings';
$lang['new'] = 'New';
$lang['manage'] = 'Manage';
$lang['users'] = 'Users';
$lang['usergroups'] = 'Usergroups';
$lang['admincp'] = 'AdminCP';
$lang['delete'] = 'Delete';
$lang['update'] = 'Update';
$lang['columns'] = 'Columns';
$lang['filters'] = 'Filters';
$lang['add_filter'] = 'Add Filter';
$lang['is'] = 'is';
$lang['is_not'] = 'is not';
$lang['or'] = 'or';
$lang['created'] = 'Created';
$lang['updates'] = 'Updates';
$lang['new_password'] = 'New Password';
$lang['assigned_tickets_x'] = 'Assigned Tickets ({1})';
$lang['updated'] = 'Updated';
$lang['none'] = 'None';
$lang['attach_file'] = 'Attach file';
$lang['revision'] = 'Revision';
$lang['files'] = 'Files';
$lang['source'] = 'Source';
$lang['select_repository'] = 'Select Repository:';
$lang['message'] = 'Message';
$lang['show'] = 'Show';
$lang['1_week'] = '1 week';
$lang['2_weeks'] = '2 weeks';
$lang['4_weeks'] = '4 weeks';
$lang['1_year'] = '1 year';
$lang['All_time'] = 'All time';
$lang['Watch_this_project'] = 'Watch this project';
$lang['Unwatch_this_project'] = 'Unwatch this project';
$lang['Watch_this_ticket'] = 'Watch this ticket';
$lang['Unwatch_this_ticket'] = 'Unwatch this ticket';
$lang['Watch_this_milestone'] = 'Watch this milestone';
$lang['Unwatch_this_milestone'] = 'Unwatch this milestone';
$lang['Reset_Password'] = 'Reset Password';
$lang['Reset'] = 'Reset';
$lang['x_password_reset'] = '{1} Password Reset';
$lang['password_reset_message'] = 'Hello {1}, you or someone requested a password reset for your account.'."\r\n\r\n".
	'Please follow the URL below to reset your password:'."\r\n".
	'http://'.$_SERVER['HTTP_HOST'].$uri->anchor('user','resetpass?hash={2}');
$lang['Wiki'] = 'Wiki';
$lang['completed_on_x'] = 'Completed on {1}';
$lang['All'] = 'All';
$lang['x_timeline'] = '{1} Timeline';
$lang['x_tickets'] = '{1} Tickets';

// Notifications
$lang['x_x_notification'] = '{1}: {2} notification';
$lang['notification_ticket_created'] = "Hello {1},\r\n\r\nA new ticket has been created on {2}.\r\n\r\n#{3}: {4}\r\n{5}";
$lang['notification_ticket_updated'] = "Hello {1},\r\n\r\nAn update has been made to ticket #{3}: {4}\r\n{5}";
$lang['notification_milestone_completed'] = "Hello {1},\r\n\r\nThe Milestone {3} has been marked as completed.\r\n{4}";

// Tickets
$lang['ticket_x'] = 'Ticket #{1}';
$lang['update_ticket'] = 'Update Ticket';
$lang['ticket_properties'] = 'Ticket Properties';
$lang['attach'] = 'Attach';
$lang['action'] = 'Action';
$lang['mark_as'] = 'Mark as';
$lang['close_as'] = 'Close as';
$lang['reopen_as'] = 'Reopen as';
$lang['ticket_history'] = 'Ticket History';
$lang['delete_ticket_confirm'] = 'Are you sure you want to delete this ticket?';
$lang['ticket_history_type'] = 'Changed Type from {1} to {2}';
$lang['ticket_history_version'] = 'Changed Version from {1} to {2}';
$lang['ticket_history_milestone'] = 'Changed Milestone from {1} to {2}';
$lang['ticket_history_component'] = 'Changed Component from {1} to {2}';
$lang['ticket_history_severity'] = 'Changed Severity from {1} to {2}';
$lang['ticket_history_priority'] = 'Changed Priority from {1} to {2}';
$lang['ticket_history_summary'] = 'Changed Summary from \'{1}\' to \'{2}\'';
$lang['ticket_history_assigned_to'] = 'Changed Assignee from {1} to {2}';
$lang['ticket_history_status_open'] = 'Opened Ticket';
$lang['ticket_history_status_mark'] = 'Changed Status from {1} to {2}';
$lang['ticket_history_status_close'] = 'Closed ticket as {2}';
$lang['ticket_history_status_reopen'] = 'Reopened ticket as {2}';
$lang['ticket_history_private_public'] = 'Marked ticket as public';
$lang['ticket_history_private_private'] = 'Marked ticket as private';

// Roadmap
$lang['x_roadmap'] = '{1} Roadmap';
$lang['x_late'] = '{1} late';
$lang['due_x_from_now'] = 'Due {1} from now';
$lang['active_tickets'] = 'Active tickets';
$lang['closed_tickets'] = 'Closed tickets';
$lang['total_tickets'] = 'Total tickets';
$lang['milestone_x'] = 'Milestone {1}';
$lang['milestones'] = 'Milestones';

// Timeline
$lang['timeline_open_ticket'] = '{1} opened Ticket #{2} ({3})';
$lang['timeline_close_ticket'] = '{1} closed Ticket #{2} ({3})';
$lang['timeline_reopen_ticket'] = '{1} reopened Ticket #{2} ({3})';

// Time
$lang['x_seconds'] = '{1} seconds';
$lang['x_minutes'] = '{1} minutes';
$lang['x_hours'] = '{1} hours';
$lang['x_days'] = '{1} days';
$lang['x_weeks'] = '{1} weeks';
$lang['x_months'] = '{1} months';
$lang['x_years'] = '{1} years';
$lang['x_second'] = '{1} second';
$lang['x_minute'] = '{1} minute';
$lang['x_hour'] = '{1} hour';
$lang['x_day'] = '{1} day';
$lang['x_week'] = '{1} week';
$lang['x_month'] = '{1} month';
$lang['x_year'] = '{1} year';
$lang['x_and_x'] = '{1} and {2}';
$lang['x_ago'] = '{1} ago';
$lang['opened_x_ago'] = 'Opened {1} ago';
$lang['last_updated_x_ago'] = 'Last updated {1} ago';
$lang['last_updated_never'] = 'Last updated Never';

// AdminCP
$lang['traq_admincp'] = 'Traq AdminCP';
$lang['overview'] = 'Overview';
$lang['view_site'] = 'View Site';
$lang['manage_types'] = 'Mange Types';
$lang['manage_priorities'] = 'Manage Priorities';
$lang['manage_severities'] = 'Manage Severities';
$lang['manage_status_types'] = 'Manage Status Types';
$lang['update_available_x_download'] = 'Update available: <a href="{1}">{2}</a> [<a href="{3}">Download</a>]';
$lang['general'] = 'General';
$lang['traq_name'] = 'Traq Name';
$lang['traq_name_description'] = 'The name of the Traq installation.';
$lang['seo_friendly_urls'] = 'SEO Friendly URL\'s';
$lang['seo_friendly_urls_description'] = 'Removes \'index.php\' from the URL.';
$lang['recaptcha_public_api_key'] = 'Public API Key';
$lang['recaptcha_public_api_key_description'] = 'Enter the public API key from reCAPTCHA.';
$lang['recaptcha_private_api_key'] = 'Private API Key';
$lang['recaptcha_private_api_key_description'] = 'Enter the private API key from reCAPTCHA.';
$lang['enable_recaptcha'] = 'Enable reCAPTCHA';
$lang['enable_recaptcha_description'] = 'Toggle reCAPTCHA on or off.';
$lang['project'] = 'Project';
$lang['new_project'] = 'New Project';
$lang['edit_project'] = 'Edit Project';
$lang['project_name'] = 'Project Name';
$lang['project_name_description'] = 'The name of the project.';
$lang['project_managers'] = 'Project Managers';
$lang['project_managers_description'] = 'Select the users that can manage this project.';
$lang['codename'] = 'Codename';
$lang['codename_description'] = 'The project codename.';
$lang['slug'] = 'Slug';
$lang['project_slug_description'] = 'The name of the project to be used in the URI.<br /><small>traq.yoursite.com/<strong>project-slug</strong></small>';
$lang['project_description'] = 'Project Description';
$lang['create'] = 'Create';
$lang['display_order'] = 'Display Order';
$lang['project_dispay_order_description'] = 'The position the project should show up on the project list.';
$lang['plugins'] = 'Plugins';
$lang['install'] = 'Install';
$lang['disable'] = 'Disable';
$lang['enable'] = 'Enable';
$lang['uninstall'] = 'Uninstall';
$lang['plugin'] = 'Plugin';
$lang['active_plugins'] = 'Active Plugins';
$lang['disabled_plugins'] = 'Disabled Plugins';
$lang['author'] = 'Author';
$lang['edit'] = 'Edit';
$lang['create_plugin'] = 'Create Plugin';
$lang['edit_plugin'] = 'Edit Plugin';
$lang['create_hook'] = 'Create Hook';
$lang['no_plugins'] = 'No Plugins';
$lang['plugin_file'] = 'Plugin File';
$lang['plugin_file_description'] = 'Locate the plugin XML file on your computer.';
$lang['install_plugin'] = 'Install Plugin';
$lang['plugin_name'] = 'Plugin Name';
$lang['plugin_name_description'] = 'The name of the plugin.';
$lang['plugin_author'] = 'Plugin Author';
$lang['plugin_author_description'] = 'Plugin Author\'s name.';
$lang['plugin_website'] = 'Plugin Website';
$lang['plugin_website_description'] = 'The website of where the plugin is hosted.<br /><small>This will help people find updates for the plugin.</small>';
$lang['plugin_version'] = 'Plugin Version';
$lang['plugin_version_description'] = 'The plugin\'s version.';
$lang['plugin_install_sql'] = 'Install SQL';
$lang['plugin_uninstall_sql'] = 'Uninstall SQL';
$lang['plugin_hooks_for_x'] = 'Plugin Hooks for {1}';
$lang['hook'] = 'Hook';
$lang['code'] = 'Code';
$lang['hooks'] = 'Hooks';
$lang['export'] = 'Export';
$lang['plugin_hooks'] = 'Plugin Hooks';
$lang['hook_description'] = 'The name of the hook.';
$lang['new_hook'] = 'New Hook';
$lang['edit_hook'] = 'Edit Hook';
$lang['execution_order'] = 'Execution Order';
$lang['hook_execution_order_description'] = 'The order the code should be executed in.<br /><small>1 first, 2 second, and so on.</small>';
$lang['select_hook'] = 'Select Hook';
$lang['title'] = 'Title';
$lang['hook_title_description'] = 'Title of the hook code.';
$lang['hook_plugin_description'] = 'The plugin the hook belongs to.';
$lang['uninstall_plugin_confirm'] = 'Are you sure you want to uninstall this plugin?';
$lang['delete_plugin_hook_confirm'] = 'Are you sure you want to delete this hook?';
$lang['new_milestone'] = 'New Milestone';
$lang['manage_milestones'] = 'Manage Milestones';
$lang['new_version'] = 'New Version';
$lang['manage_versions'] = 'Manage Versions';
$lang['new_component'] = 'New Component';
$lang['manage_components'] = 'Manage Components';
$lang['no_projects'] = 'No Projects';
$lang['no_milestones'] = 'No Milestones';
$lang['no_components'] = 'No Components';
$lang['milestone_name_description'] = 'Name or version of the milestone.';
$lang['milestone_slug_description'] = 'Used in the URI.<br /><small>traq.yoursite.com/project-slug/<strong>milestone-slug</strong></small>';
$lang['milestone_codename_description'] = 'Codename for the milestone.';
$lang['milestone_project_description'] = 'The project the milestone belongs to.';
$lang['milestone_changelog'] = 'Milestone Changelog';
$lang['due'] = 'Due';
$lang['milestone_due_description'] = 'The date when the milestone is due. <small>(DD/MM/YYYY)</small><br /><small>Leave empty for no due date</small>';
$lang['milestone_displayorder_description'] = 'The order the milestone will be displayed in.';
$lang['milestone_description'] = 'Milestone Description';
$lang['name'] = 'Name';
$lang['component_name_description'] = 'Component\'s name.';
$lang['component_project_description'] = 'Project the component belongs to.';
$lang['components'] = 'Components';
$lang['edit_milestone'] = 'Edit Milestone';
$lang['milestone_status_description'] = 'The status of the milestone.';
$lang['active'] = 'Active';
$lang['completed'] = 'Completed';
$lang['cancelled'] = 'Cancelled';
$lang['edit_component'] = 'Edit Component';
$lang['versions'] = 'Versions';
$lang['no_versions'] = 'No Versions';
$lang['version_name_description'] = 'The name/version.';
$lang['version_project_description'] = 'The project the version belongs to.';
$lang['edit_version'] = 'Edit Version';
$lang['allow_registration'] = 'Allow Registration';
$lang['allow_registration_description'] = 'Allow Users to register?';
$lang['save_settings'] = 'Save Settings';
$lang['fill_in_to_add_new_type'] = 'Fill in to add new Type.';
$lang['fill_in_to_add_new_status'] = 'Fill in to add new Status.';
$lang['fill_in_to_add_new_severity'] = 'Fill in to add new Severity.';
$lang['open'] = 'Open';
$lang['closed'] = 'Closed';
$lang['bullet'] = 'Bullet';
$lang['date_and_time'] = 'Date and Time';
$lang['date_time_format'] = 'Date and Time format.';
$lang['date_time_format_description'] = 'The format of the date and time, see <a href="http://php.net/date">http://php.net/date</a>.';
$lang['templates'] = 'Templates';
$lang['functions'] = 'Functions';
$lang['handlers'] = 'Handlers';
$lang['new_user'] = 'New User';
$lang['new_usergroup'] = 'New Usergroup';
$lang['manage_groups'] = 'Manage Groups';
$lang['edit_user'] = 'Edit User';
$lang['username_description'] = 'The users login name.';
$lang['password_description'] = 'The users login password.<br /><small>Leave blank to keep current password.</small>';
$lang['name_description'] = 'The users name.';
$lang['email_description'] = 'The users email address.';
$lang['group'] = 'Group';
$lang['group_description'] = 'The group the user belongs to.';
$lang['edit_usergroup'] = 'Edit Usergroup';
$lang['usergroup_name_description'] = 'The name of the Usergroup';
$lang['administrator'] = 'Administrator';
$lang['usergroup_admin_description'] = 'Select yes if you want this group to access the AdminCP.';
$lang['yes'] = 'Yes';
$lang['no'] = 'No';
$lang['create_tickets'] = 'Create Tickets';
$lang['usergroup_create_tickets_description'] = 'Whether or not the group and create tickets.';
$lang['update_tickets'] = 'Update Tickets';
$lang['usergroup_update_tickets_description'] = 'Whether or not the group and update tickets.';
$lang['delete_tickets'] = 'Delete Tickets';
$lang['usergroup_delete_tickets_description'] = 'Whether or not the group and delete tickets.';
$lang['add_attachments'] = 'Add Attachments';
$lang['usergroup_add_attachments_description'] = 'Whether or not the group and add attachments to tickets.';
$lang['repositories'] = 'Repositories';
$lang['new_repository'] = 'New Repository';
$lang['manage_repositories'] = 'Manage Repositories';
$lang['edit_repository'] = 'Edit Repository';
$lang['repository_name_description'] = 'The name of the repository.';
$lang['repository_project_description'] = 'The project this repository belongs to.';
$lang['repository_type_description'] = 'The type of the repository.';
$lang['repository_location_description'] = 'The location where the repository is.<br /><small>Example: http://project.googlecode.com/svn</small>';
$lang['no_repositories'] = 'No Repositories';
$lang['repository'] = 'Repository';
$lang['location'] = 'Location';
$lang['statistics'] = 'Statistics';
$lang['Theme'] = 'Theme';
$lang['theme_description'] = 'The theme used by Traq';
$lang['Language'] = 'Language';
$lang['language_description'] = 'Select the language Traq should be displayed in.';
$lang['New_Page'] = 'New Page';
$lang['no_pages'] = 'No Pages';
$lang['New_Wiki_Page'] = 'New Wiki Page';
$lang['Edit_Wiki_Page'] = 'Edit Wiki Page';
$lang['page_title_description'] = 'Title of the page.';
$lang['page_project_description'] = 'The project this Wiki page belongs to.';
$lang['Body'] = 'Body';
$lang['comment_on_tickets'] = 'Comment on tickets';
$lang['usergroup_comment_on_tickets_description'] = 'Select yes if you want this group to comment on tickets.';
$lang['ticket_template'] = 'Ticket Template';
$lang['edit_ticket_template_x'] = 'Edit Ticket Template: {1}';
$lang['template'] = 'Template';

// Confirms
$lang['confirm_delete_x'] = 'Are you sure you want to delete {1}?';

// Success messages
$lang['success_reset_pass_email_sent'] = 'Email sent, check your inbox for instructions.';

// Errors
$lang['error_summary_empty'] = 'Summary cannot be blank.';
$lang['error_body_empty'] = 'Description cannot be blank.';
$lang['error_name_empty'] = 'Please fill in your Name.';
$lang['error_recaptcha'] = 'reCaptcha must be valid.';
$lang['error_invalid_username_or_password'] = 'Invalid Username or Password.';
$lang['error_username_taken'] = 'Username is unavailable.';
$lang['error_username_empty'] = 'You must enter a Username.';
$lang['error_password_empty'] = 'Cannot have a blank password.';
$lang['error_password_nomatch'] = 'Passwords dont match';
$lang['error_email_empty'] = 'You must enter your email.';
$lang['error_project_name_blank'] = 'The Project Name cannot be blank.';
$lang['error_project_slug_blank'] = 'The Project Slug cannot be blank.';
$lang['error_project_slug_taken'] = 'The Project Slug is already in use.';
$lang['error_plugin_name_blank'] = 'Plugin Name cannot be empty.';
$lang['error_plugin_author_blank'] = 'Plugin Author cannot be empty.';
$lang['error_plugin_version_blank'] = 'Plugin Version cannot be empty.';
$lang['error_title_blank'] = 'Title cannot be empty.';
$lang['error_select_a_hook'] = 'You must select a hook.';
$lang['error_hook_plugin_blank'] = 'You must select a Plugin.';
$lang['error_milestone_name_blank'] = 'Milestone cannot be empty.';
$lang['error_milestone_slug_blank'] = 'Slug cannot be empty.';
$lang['error_milestone_slug_taken'] = 'Slug already in use.';
$lang['error_project_blank'] = 'You must choose a project.';
$lang['error_component_name_blank'] = 'You must enter a name.';
$lang['error_version_name_blank'] = 'Name cannot be blank.';
$lang['error_enter_password'] = 'You must enter your current password.';
$lang['error_name_empty'] = 'You must enter a Name.';
$lang['error_name_taken'] = 'That name is already in use.';
$lang['error_location_empty'] = 'You must enter a location.';
$lang['error_user_not_found'] = 'User not found.';
$lang['error_resetting_password'] = 'Unable to reset password.';
$lang['error_title_empty'] = 'You must enter a title.';
$lang['error_title_taken'] = 'Title already in use.';
$lang['error'] = 'Error';
$lang['error_no_permission'] = 'You don\'t have permission to view this page.';
$lang['page_x_not_found'] = 'The page \'{1}\' could not be found.';
?>