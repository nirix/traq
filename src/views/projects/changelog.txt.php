<?php
foreach ($milestones as $milestone):
    echo $milestone->name . PHP_EOL;

    foreach ($milestone->tickets()->fetchAll() as $issue):
        echo "- {$issue->summary}" . PHP_EOL;
    endforeach;

    echo PHP_EOL;
endforeach;
