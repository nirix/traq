<div class="progress">
	<table class="progress_bar">
		<tr>
		<?php if ($milestone->ticket_count('total') == 0) { ?>
			<td class="open"><?php echo HTML::link('', $project->href('tickets') . '?status=open'); ?></td>
		<?php } else { ?>
			<?php if ($milestone->ticket_count('closed') > 0) { ?>
			<td class="closed" style="width:<?php echo $milestone->ticket_count('closed_percent'); ?>%;"><?php echo HTML::link('', $project->href('tickets') . '?status=closed'); ?></td>
			<?php } ?>
			<?php if ($milestone->ticket_count('open') > 0) { ?>
			<td class="open" style="width:<?php echo $milestone->ticket_count('open_percent'); ?>%;"><?php echo HTML::link('', $project->href('tickets') . '?status=open'); ?></td>
			<?php } ?>
		<?php } ?>
		</tr>
	</table>
</div>