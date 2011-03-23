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
	<?php _l('x_open', $milestone['tickets']['open']); ?>, <?php _l('x_closed', $milestone['tickets']['closed']); ?>
</div>