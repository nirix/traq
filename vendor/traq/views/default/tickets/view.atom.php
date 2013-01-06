<?php
use traq\helpers\Atom;

// Entries
$entries = array();
$updates = $ticket->history->order_by('id', 'DESC')->exec()->fetch_all();
foreach (array_reverse($updates) as $update) {
    $content = array();

    if (is_array($update->changes)) {
        $content[] = "<ul>";
        foreach ($update->changes as $change) {
            $content[] = "    <li>" . View::get('tickets/_history_change_bit', array('change' => $change)) . "</li>";
        }
        $content[] = "</ul>";
    }

    if ($update->comment != '') {
        $content[] = "<hr />";
        $content[] = format_text($update->comment);
    }

    $entries[] = array(
        'title' => l('update_x', count($entries)+1),
        'id' => "tickets:{$ticket->ticket_id}:update:{$update->id}",
        'updated' => Time::date("c", $update->created_at),
        'link' => "http://" . $_SERVER['HTTP_HOST'] . Request::base($ticket->href()),
        'author' => array(
            'name' => $update->user->name
        ),
        'content' => array(
            'type' => "XHTML",
            'data' => implode(PHP_EOL, $content)
        ),
    );
}

// Make feed
$entries = array_reverse($entries);
$feed = new Atom(array(
    'title' => l('x_x_history_feed', $project->name, $ticket->summary),
    'link' => "http://" . $_SERVER['HTTP_HOST'] . Request::base(),
    'feed_link' => "http://" . $_SERVER['HTTP_HOST'] . Request::requestUri(),
    'updated' => $entries[0]['updated'],
    'entries' => $entries,
));

// Output feed
header("Content-type: text/plain");
print($feed->build());
