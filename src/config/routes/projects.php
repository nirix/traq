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
Router::get('ticket', '/{pslug}/issues/{id}', "{$ns}\\Tickets::show");

// Changelog
Router::get('changelog', '/{pslug}/changelog', "{$ns}\\Projects::changelog");

// Wiki
Router::get('wiki', '/{pslug}/wiki', "{$ns}\\Wiki::show", ['slug' => 'main']);
Router::get('wiki_pages', '/{pslug}/wiki/_pages', "{$ns}\\Wiki::pages");
Router::get('wiki_page', '/{pslug}/wiki/{slug}', "{$ns}\\Wiki::show");
Router::get('wiki_revisions', '/{pslug}/wiki/{slug}/_revisions', "{$ns}\\Wiki::revisions");
Router::get('wiki_revision', '/{pslug}/wiki/{slug}/_revisions/{id}', "{$ns}\\Wiki::revision");
