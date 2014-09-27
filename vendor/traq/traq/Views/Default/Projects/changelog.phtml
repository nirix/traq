<div class="changelog content">
	<h2 id="page_title"><?php echo l('changelog'); ?></h2>

	<style>
	<?php foreach ($types as $type) { ?>
		.type_<?php echo $type->id; ?>:before {
			content: "<?php echo utf8_encode($type->bullet); ?>";
		}
	<?php } ?>
	</style>
	<?php foreach ($milestones as $milestone) { ?>
		<div id="changeset">
			<h3><?php echo $milestone->name; ?></h3>
			<ul>
			<?php foreach ($milestone->tickets->exec()->fetch_all() as $ticket) {
				if (!$types[$ticket->type_id]->changelog or !$ticket->status->changelog) {
					continue;
				}
			?>
				<li class="type_<?php echo $ticket->type_id; ?>"><?php echo HTML::link($ticket->summary, $ticket->href()); ?></li>
			<?php } ?>
			</ul>
		</div>
	<?php } ?>
</div>