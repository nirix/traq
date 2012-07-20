<?php foreach($milestones as $milestone) { ?>
<?php echo l('milestone_x',$milestone['milestone'])?>
	<?php foreach($milestone['changes'] as $change) { ?>
	<span class="type_bullet"><?php echo $types[$change['type']]['bullet']?></span> <?php echo $change['summary']?><br />
	<?php } ?>
	<?php echo $milestone['changelog']?>
<?php } ?>

<?php
foreach ($milestones as $milestone) {
	echo l('milestone_x', $milestone['milestone']) . PHP_EOL;
	foreach ($milestone['changes'] as $change) {
		echo $types[$change['type']]['bullet'] . $change['summary'] . PHP_EOL;
	}
	echo $milestone['changelog_plaintext'];
}