<h2><?php _l('tickets')?></h2>
<table class="list tickets">
	<thead>
		<?php foreach($columns as $column):
		echo Tickets::column_header($column).PHP_EOL;
		endforeach; ?>
	</thead>
	<tbody>
		<?php foreach($tickets as $ticket): ?>
		<tr class="<?php echo altbg()?>">
			<?php foreach($columns as $column):
			echo Tickets::column_content($column, $ticket).PHP_EOL;
			endforeach; ?>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>