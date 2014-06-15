<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace Traq\Translations;

use Traq\Traq;
use Radium\Language;

/**
 * Australian English translation.
 *
 * @author Jack P.
 * @copyright (C) Jack P.
 * @package Traq\Translations
 */
$enAU = new Language(function($t){
    $t->name   = "English (Australian)";
    $t->locale = "enAU";

    $t->strings = array(
        'copyright' => "Powered by Traq " . Traq::version() . " &copy; 2009-" . date("Y") . " Traq.io",

        // Users
        'profile'  => "Profile",
        'usercp'   => "UserCP",
        'register' => "Register",
        'login'    => "Login",
        'logout'   => "Logout",
        'username' => "Username",
        'password' => "Password",

        // Projects
        'projects' => "Projects",

        // Roadmap
        'roadmap' => "Roadmap",

        // Issues
        'issues'       => "Issues",
        'create_issue' => "Create Issue",

        // Changelog
        'changelog' => "Changelog",

        // Wiki
        'wiki' => "Wiki",

        // Timeline
        'timeline'                     => "Timeline",
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

        // Filters
        'filter_events' => "Filter Events",
        'apply_filters' => "Apply Filters",

        // Timeline filters
        'timeline.filters.new_tickets'           => "New tickets",
        'timeline.filters.tickets_opened_closed' => "Tickets open/closed",
        'timeline.filters.ticket_updates'        => "Ticket updates",
        'timeline.filters.ticket_comments'       => "Ticket comments",
        'timeline.filters.ticket_moves'          => "Ticket migrations",
        'timeline.filters.milestones'            => "Milestones",
        'timeline.filters.wiki_pages'            => "Wiki pages",

        // AdminCP
        'admincp' => "AdminCP",

        // Pagination
        'next'     => "Next",
        'previous' => "Previous",

        // Errors
        'errors.404.title'   => "I accidentally the whole page...",
        'errors.404.message' => "Well this is awkward, it seems the page '{1}' doesn't exist.",
        'errors.invalid_username_or_password' => "Invalid username and/or password.",
        'errors.account.activation_required'  => "You must first activate your account.",
    );
});
