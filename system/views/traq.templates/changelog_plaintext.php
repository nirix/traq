<?php
header("Content-type: text/plain");
foreach ($milestones as $milestone) {
	echo l('milestone_x', $milestone['milestone']) . PHP_EOL;
	foreach ($milestone['changes'] as $change) {
		echo $types[$change['type']]['bullet'] . ' ' . $change['summary'] . PHP_EOL;
	}
	echo trim(strip_tags($milestone['changelog_plaintext'])) . PHP_EOL;
	echo PHP_EOL;
}