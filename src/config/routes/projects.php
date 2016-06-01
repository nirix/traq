<?php
use Avalon\Routing\Router;

// -----------------------------------------------------------------------------
// Project routes
Router::get('projects', '/projects', "{$ns}Projects::index");
Router::get('project', '/{pslug}', "{$ns}Projects::show");
Router::get('changelog', '/{pslug}/changelog', "{$ns}Projects::changelog");

// Timeline
Router::get('timeline', '/{pslug}/timeline', "{$ns}Timeline::index");
Router::post('timeline_filter', '/{pslug}/timeline', "{$ns}Timeline::setFilters");
Router::delete('timeline_delete_event', '/{pslug}/timeline/{id}/delete', "{$ns}Timeline::deleteEvent");

// Roadmap
Router::get('roadmap', '/{pslug}/roadmap', "{$ns}Roadmap::index");
Router::get('roadmap_all', '/{pslug}/roadmap/all', "{$ns}Roadmap::index", ['filter' => 'all']);
Router::get('roadmap_completed', '/{pslug}/roadmap/completed', "{$ns}Roadmap::index", ['filter' => 'completed']);
Router::get('roadmap_cancelled', '/{pslug}/roadmap/cancelled', "{$ns}Roadmap::index", ['filter' => 'cancelled']);

Router::get('milestone', '/{pslug}/milestone/{slug}', "{$ns}Roadmap::show");

// Tickets
Router::get('tickets', '/{pslug}/tickets', "{$ns}TicketListing::index");
Router::get('ticket', '/{pslug}/tickets/{id}', "{$ns}Tickets::show");

Router::post('tickets_set_columns', '/{pslug}/issues/set-columns', "{$ns}TicketListing::setColumns");
Router::post('tickets_set_filters', '/{pslug}/issues/set-filters', "{$ns}TicketListing::setFilters");

// Wiki
Router::get('wiki', '/{pslug}/wiki', "{$ns}Wiki::show", ['slug' => 'main']);
Router::get('wiki_pages', '/{pslug}/wiki/_pages', "{$ns}Wiki::pages");
Router::get('wiki_new', '/{pslug}/wiki/_new', "{$ns}Wiki::new");
Router::post('wiki_create', '/{pslug}/wiki', "{$ns}Wiki::create");
Router::get('wiki_page', '/{pslug}/wiki/{slug}', "{$ns}Wiki::show");
Router::get('wiki_revisions', '/{pslug}/wiki/{slug}/_revisions', "{$ns}Wiki::revisions");
Router::get('wiki_revision', '/{pslug}/wiki/{slug}/_revisions/{id}', "{$ns}Wiki::revision");
Router::get('wiki_edit', '/{pslug}/wiki/{slug}/_edit', "{$ns}Wiki::edit");
Router::put('wiki_save', '/{pslug}/wiki/{slug}', "{$ns}Wiki::save")->method(['PUT', 'PATCH']);
Router::delete('wiki_delete', '/{pslug}/wiki/{slug}', "{$ns}Wiki::destroy");
