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
	<?php echo HTML::link(baseurl($traq->project['slug'],'tickets').'?milestone='.$milestone['slug'].'&status=open', l('x open', $milestone['ticket_count']['open']))?>,
	<?php echo HTML::link(baseurl($traq->project['slug'],'tickets').'?milestone='.$milestone['slug'].'&status=closed', l('x closed', $milestone['ticket_count']['closed']))?>
</div>