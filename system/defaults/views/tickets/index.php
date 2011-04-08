<table class="list tickets">
	<thead>
		<?php foreach($columns as $column):
		echo Tickets::column_header($column).PHP_EOL;
		endforeach; ?>
	</thead>
	<tbody>
		<?php foreach($tickets as $ticket): ?>
		<tr>
			<?php foreach($columns as $column):
			echo Tickets::column_content($column, $ticket).PHP_EOL;
			endforeach; ?>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>