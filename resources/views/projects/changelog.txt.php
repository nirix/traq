<?php
foreach ($milestones as $milestone) :
    echo $milestone['name'], PHP_EOL;

    foreach ($milestone['tickets'] as $ticket) :
        echo "- {$ticket['summary']}", PHP_EOL;
    endforeach;

    echo PHP_EOL;
endforeach;
