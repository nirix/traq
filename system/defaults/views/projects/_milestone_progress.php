<table class="progress">
	<tbody>
		<tr>
			<td class="closed" style="width: <?php echo $milestone['progress']['closed']?>%;"></td>
			<td class="open" style="width: <?php echo $milestone['progress']['open']?>%;"></td>
		</tr>
	</tbody>
</table>
<div class="percent"><?php echo $milestone['progress']['closed']?>%</div>
<div class="progress-info">
	<?php _l('x open', $milestone['ticket_count']['open']); ?>, <?php _l('x closed', $milestone['ticket_count']['closed']); ?>
</div>