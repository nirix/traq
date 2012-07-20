<?php
header("Content-type: text/plain");
foreach ($milestones as $milestone) {
	echo l('milestone_x', $milestone['milestone']) . PHP_EOL;
	foreach ($milestone['changes'] as $change) {
		echo $types[$change['type']]['bullet'] . $change['summary'] . PHP_EOL;
	}
	echo $milestone['changelog_plaintext'];
}
echo PHP_EOL;