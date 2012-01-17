<div id="ticket_info">
	<h2 id="ticket_summary"><?php echo $ticket->summary; ?></h2>
	
	<h3><?php echo l('description'); ?></h3>
	<?php echo format_text($ticket->body, true); ?>
</div>