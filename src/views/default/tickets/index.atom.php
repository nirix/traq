<?php

use avalon\helpers\Time;
use Avalon\Http\Request;
use Traq\Helpers\Atom;

// Entries
$entries = array();
foreach ($tickets as $ticket) {
    $entries[] = array(
        'title' => $ticket->summary,
        'id' => "tickets:{$ticket->ticket_id}",
        'updated' => Time::date("c", $ticket->created_at),
        'link' => absolute_url($ticket->href()),
        'author' => array(
            'name' => $ticket->user->name
        ),
        'content' => array(
            'type' => "XHTML",
            'data' => $ticket->body
        ),
    );
}

// Make feed
$feed = new Atom(array(
    'title' => l('x_ticket_feed', $project->name),
    'link' => absolute_url(),
    'feed_link' => origin() . Request::requestUri(),
    'updated' => $entries[0]['updated'],
    'entries' => $entries,
));

// Output feed
header("Content-type: text/plain");
print($feed->build());
