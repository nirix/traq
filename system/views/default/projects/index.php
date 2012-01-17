<div class="content">
	<h2 id="page_title"><?php echo l('projects'); ?></h2>
	<ul>
		<?php foreach($projects as $project) { ?>
		<li>
			<h3><a href="<?php echo Request::base($project->slug)?>"><?php echo $project->name; ?></a></h3>
				<?php echo format_text($project->info); ?>
		</li>
		<?php } ?>
	</ul>
</div>