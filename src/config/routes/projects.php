<?php
use Avalon\Routing\Router;

Router::get('project', '/{pslug}', "{$ns}\\Projects::show");

// Timeline
Router::get('timeline', '/{pslug}/timeline', "{$ns}\\Timeline::index");
Router::post('timeline_set_filters', '/{pslug}/timeline', "{$ns}\\Timeline::setFilters");

// Roadmap
Router::get('roadmap', '/{pslug}/roadmap', "{$ns}\\Roadmap::index");
Router::get('roadmap_completed', '/{pslug}/roadmap/completed', "{$ns}\\Roadmap::index", ['filter' => 'completed']);
Router::get('roadmap_all', '/{pslug}/roadmap/all', "{$ns}\\Roadmap::index", ['filter' => 'all']);
Router::get('roadmap_cancelled', '/{pslug}/roadmap/cancelled', "{$ns}\\Roadmap::index", ['filter' => 'cancelled']);
Router::get('milestone', '/{pslug}/roadmap/{mslug}', "{$ns}\\Roadmap::show");

// Issues
Router::get('tickets', '/{pslug}/issues', "{$ns}\\TicketListing::index");
Router::post('tickets_set_columns', '/{pslug}/issues/set-columns', "{$ns}\\TicketListing::setColumns");
Router::post('tickets_set_filters', '/{pslug}/issues/set-filters', "{$ns}\\TicketListing::setFilters");
Router::get('new_ticket', '/{pslug}/issues/new', "{$ns}\\Tickets::new");
Router::post('create_ticket', '/{pslug}/issues/new', "{$ns}\\Tickets::create");
Router::get('ticket', '/{pslug}/issues/{id}', "{$ns}\\Tickets::show");

// Changelog
Router::get('changelog', '/{pslug}/changelog', "{$ns}\\Projects::changelog");

// Wiki
Router::get('wiki', '/{pslug}/wiki', "{$ns}\\Wiki::show", ['slug' => 'main']);
Router::get('wiki_new', '/{pslug}/wiki/_new', "{$ns}\\Wiki::new");
Router::post('wiki_create', '/{pslug}/wiki/_new', "{$ns}\\Wiki::create");
Router::get('wiki_edit', '/{pslug}/wiki/{slug}/_edit', "{$ns}\\Wiki::edit");
Router::post('wiki_save', '/{pslug}/wiki/{slug}/_edit', "{$ns}\\Wiki::save");
Router::get('wiki_delete', '/{pslug}/wiki/{slug}/_delete', "{$ns}\\Wiki::destroy");
Router::get('wiki_pages', '/{pslug}/wiki/_pages', "{$ns}\\Wiki::pages");
Router::get('wiki_page', '/{pslug}/wiki/{slug}', "{$ns}\\Wiki::show");
Router::get('wiki_revisions', '/{pslug}/wiki/{slug}/_revisions', "{$ns}\\Wiki::revisions");
Router::get('wiki_revision', '/{pslug}/wiki/{slug}/_revisions/{id}', "{$ns}\\Wiki::revision");
