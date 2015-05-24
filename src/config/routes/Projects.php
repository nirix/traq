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

namespace Traq\config\routes;

use Avalon\Routing\Router;

/**
 * Project routes.
 *
 * @author Jack Polgar <jack@polgar.id.au>
 * @since 4.0.0
 */
class Projects
{
    public static function register(Router $r)
    {
        $traq = "Traq\\Controllers";

        // Index and show
        $r->get("/projects", 'projects')->to("{$traq}\Projects::index");
        $r->get('/{project_slug}', 'show_project')->to("{$traq}\Projects::show");

        // --------------------------------------------------
        // Timeline
        $r->route('/{project_slug}/timeline', 'timeline')->to("{$traq}\Timeline::index")
            ->method(['get','post']);
        $r->get('/{project_slug}/timeline/{event_id}/delete', 'delete_timeline_event')
            ->to("{$traq}\Timeline::deleteEvent");

        // --------------------------------------------------
        // Roadmap
        $r->get('/{project_slug}/roadmap', 'roadmap')
            ->to("{$traq}\Roadmap::index");

        $r->get('/{project_slug}/roadmap/all', 'roadmap_all')
            ->to("{$traq}\Roadmap::index", ['filter' => 'all']);

        $r->get('/{project_slug}/roadmap/completed', 'roadmap_completed')
            ->to("{$traq}\Roadmap::index", ['filter' => 'completed']);

        $r->get('/{project_slug}/roadmap/cancelled', 'roadmap_cancelled')
            ->to("{$traq}\Roadmap::index", ['filter' => 'cancelled']);

        $r->get('/{project_slug}/milestone/{slug}', 'show_milestone')
            ->to("{$traq}\Roadmap::show");

        // --------------------------------------------------
        // Issues
        $r->get('/{project_slug}/tickets')->to("{$traq}\TicketListing::index");
        $r->get('/{project_slug}/tickets/{ticket_id}')->to("{$traq}\Tickets::show");

        $r->get('/{project_slug}/issues', 'issues')
            ->to("{$traq}\TicketListing::index");

        $r->get('/{project_slug}/issues/{ticket_id}', 'show_issue')
            ->to("{$traq}\Tickets::show");

        $r->get('/{project_slug}/issues/new', 'new_issue')->to("{$traq}\Tickets::new");
        $r->post('/{project_slug}/issues/new')->to("{$traq}\Tickets::create");

        // --------------------------------------------------
        // Issue filters
        $r->post('/{project_slug}/issues/set-columns', 'set_issue_filter_columns')
            ->to("{$traq}\TicketListing::setColumns");

        $r->post('/{project_slug}/issues/update-filters', 'update_issue_filters')
            ->to("{$traq}\TicketListing::updateFilters");

        // --------------------------------------------------
        // Wiki
        $r->get('/{project_slug}/wiki', 'wiki')
            ->to("{$traq}\Wiki::show", ['slug' => 'main']);

        // New page
        $r->get('/{project_slug}/wiki/_new', 'new_wiki_page')
            ->to("{$traq}\Wiki::new");

        $r->post('/{project_slug}/wiki/_new')->to("{$traq}\Wiki::create");

        // Pages listing
        $r->get('/{project_slug}/wiki/_pages', 'wiki_pages')
            ->to("{$traq}\Wiki::pages");

        // Show page
        $r->get('/{project_slug}/wiki/{slug}', 'show_wiki_page')
            ->to("{$traq}\Wiki::show");

        // Edit page
        $r->get('/{project_slug}/wiki/{slug}/_edit', 'edit_wiki_page')
            ->to("{$traq}\Wiki::edit");

        $r->post('/{project_slug}/wiki/{slug}/_edit')->to("{$traq}\Wiki::save");

        // List revisions
        $r->get('/{project_slug}/wiki/{slug}/_revisions', 'wiki_page_revisions')
            ->to("{$traq}\Wiki::revisions");

        // Show revision
        $r->get('/{project_slug}/wiki/{slug}/_revisions/{revision}', 'show_wiki_page_revision')
            ->to("{$traq}\Wiki::revision");

        // --------------------------------------------------
        // Changelog
        $r->get('/{project_slug}/changelog', 'changelog')->to("{$traq}\Projects::changelog");
    }
}
