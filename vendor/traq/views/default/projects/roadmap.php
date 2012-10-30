<div class="roadmap content">
	<h2 id="page_title"><?php echo l('roadmap'); ?></h2>
	<ul id="milestones">
		<?php foreach($milestones as $milestone) { ?>
		<li>
			<h3><?php echo HTML::link($milestone->name . ($milestone->codename != '' ? ' <em>"' . $milestone->codename . '"</em>' : ''), $milestone->href()); ?></h3>
			<?php if ($milestone->due > 0 and $milestone->status == 1) { ?>
			<div class="due">
				<em><?php echo l('due_x', time_from_now_ago($milestone->due)); ?></em>
			</div>
			<?php } ?>
			<?php View::render('milestones/_progress', array('milestone' => $milestone)); ?>
			<div class="milestone_info">
				<?php echo format_text($milestone->info); ?>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>