<div class="content">
	<h2 id="page_title"><?php echo l('projects'); ?></h2>
	<ul id="project_list">
		<?php foreach($projects as $project) { ?>
		<li>
			<h3><?php echo HTML::link($project->name, $project->slug); ?></h3>
			<nav>
				<ul>
					<li><?php echo HTML::link(l('roadmap'), "{$project->slug}/roadmap"); ?></li>
					<li><?php echo HTML::link(l('tickets'), "{$project->slug}/tickets"); ?></li>
					<li><?php echo HTML::link(l('timeline'), "{$project->slug}/timeline"); ?></li>
				</ul>
			</nav>
			<div class="description">
				<?php echo format_text($project->info); ?>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>