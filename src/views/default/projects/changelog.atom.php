<?php

use Traq\Helpers\Atom;

$entries = array();
$updated = 0;

foreach ($milestones as $milestone) {
    $entry = array(
        'title' => $milestone->name,
        'id' => "changelog:{$project->slug}:milestone:{$milestone->slug}",
        'link' => absolute_url($milestone->href()),
        'updated' => Time::date("c", $milestone->completed_on),
        'content' => array(
            'type' => "HTML"
        )
    );

    foreach ($milestone->tickets->exec()->fetch_all() as $ticket) {
        // Set updated time for feed
        if (Time::to_unix($ticket->created_at) > $updated) {
            $updated = Time::to_unix($ticket->created_at);
        }

        // Check if this is to be displayed on the changelog
        if ($ticket->type->changelog and $ticket->status->changelog) {
            $data[] = "{$ticket->type->bullet} <a href=\"" . absolute_url($ticket->href()) . "\">{$ticket->summary}</a>";
        }
    }

    $entry['content']['data'] = implode(PHP_EOL, $data);
    $entries[] = $entry;
}

// Make feed
$feed = new Atom(array(
    'title'     => l('x_changelog_feed', $project->name),
    'link'      => absolute_url(),
    'feed_link' => origin() . Request::requestUri(),
    'updated'   => $updated == 0 ? Time::date("c") : Time::date("c", $updated),
    'entries'   => $entries,
));

// Output feed
header("Content-type: text/plain");
print($feed->build());
