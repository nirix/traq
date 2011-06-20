<div id="page-content">
	<div class="content">
		<h2 id="page-title"><?php echo l('tickets')?></h2>
	</div>
	<table class="list">
		<thead>
		<?php foreach (ticket_columns() as $column) { ?>
			<th><?php echo ticketlist_header($column)?></th>
		<?php } ?>
		</thead>
		<tbody>
		<?php foreach ($tickets as $ticket) { ?>
			<tr>
				<?php foreach (ticket_columns() as $column) { ?>
					<th><?php echo ticketlist_data($column, $ticket)?></th>
				<?php } ?>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>