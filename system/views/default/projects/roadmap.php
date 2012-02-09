<div class="content">
	<h2 id="page_title"><?php echo l('roadmap'); ?></h2>
	<ul>
		<?php foreach($milestones as $milestone) { ?>
		<li>
			<h3><?php echo HTML::link($milestone->name, $milestone->href()); ?></h3>
			<?php View::render('milestones/_progress', array('milestone' => $milestone)); ?>
			<div class="milestone_info">
				<?php echo format_text($milestone->info); ?>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>