<div id="page-content">
	<div class="content">
		<h2 id="page-title"><?php echo l('roadmap'); ?></h2>
		<ul>
			<?php foreach($milestones as $milestone) { ?>
			<li>
				<h3><a href="<?php echo Request::base($milestone->href()); ?>"><?php echo $milestone->name; ?></a></h3>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>