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
Router::delete('timeline_delete_event', '/{pslug}/timeline/{id}', "{$ns}Timeline::deleteEvent");

// Roadmap
Router::get('roadmap', '/{pslug}/roadmap', "{$ns}Roadmap::index");
Router::get('roadmap_all', '/{pslug}/roadmap/all', "{$ns}Roadmap::index", ['filter' => 'all']);
Router::get('roadmap_completed', '/{pslug}/roadmap/completed', "{$ns}Roadmap::index", ['filter' => 'completed']);
Router::get('roadmap_cancelled', '/{pslug}/roadmap/cancelled', "{$ns}Roadmap::index", ['filter' => 'cancelled']);

Router::get('milestone', '/{pslug}/milestone/{slug}', "{$ns}Roadmap::show");

// Tickets
Router::get('tickets', '/{pslug}/tickets', "{$ns}TicketListing::index");
Router::get('ticket', '/{pslug}/tickets/{id}', "{$ns}Tickets::show");
Router::get('new_ticket', '/{pslug}/tickets/new', "{$ns}Tickets::new");
Router::post('create_ticket', '/{pslug}/tickets', "{$ns}Tickets::create");

Router::post('tickets_set_columns', '/{pslug}/tickets/set-columns', "{$ns}TicketListing::setColumns");
Router::post('tickets_set_filters', '/{pslug}/tickets/set-filters', "{$ns}TicketListing::setFilters");

Router::put('update_ticket', '/{pslug}/tickets/{id}', "{$ns}Tickets::update")->method(['PUT', 'PATCH']);
Router::get('ticket_edit_description', '/{pslug}/tickets/{id}/edit-description', "{$ns}Tickets::editDescription");
Router::post('ticket_save_description', '/{pslug}/tickets/{id}/edit-description', "{$ns}Tickets::saveDescription");
Router::get('ticket_edit_comment', '/{pslug}/tickets/history/{id}/edit', "{$ns}TicketHistory::edit");
Router::delete('ticket_delete_comment', '/{pslug}/tickets/history/{id}', "{$ns}TicketHistory::destroy");

// Wiki
Router::get('wiki', '/{pslug}/wiki', "{$ns}Wiki::show", ['slug' => 'main']);
Router::get('wiki_pages', '/{pslug}/wiki/_pages', "{$ns}Wiki::pages");
Router::get('wiki_new', '/{pslug}/wiki/_new', "{$ns}Wiki::new");
Router::post('wiki_create', '/{pslug}/wiki', "{$ns}Wiki::create");
Router::get('wiki_revisions', '/{pslug}/wiki/{wslug}/_revisions', "{$ns}Wiki::revisions");
Router::get('wiki_revision', '/{pslug}/wiki/{wslug}/_revisions/{id}', "{$ns}Wiki::revision");
Router::get('wiki_edit', '/{pslug}/wiki/{wslug}/_edit', "{$ns}Wiki::edit");
Router::put('wiki_save', '/{pslug}/wiki/{wslug}', "{$ns}Wiki::save")->method(['PUT', 'PATCH']);
Router::get('wiki_page', '/{pslug}/wiki/{wslug}', "{$ns}Wiki::show");
Router::delete('wiki_delete', '/{pslug}/wiki/{wslug}', "{$ns}Wiki::destroy");
