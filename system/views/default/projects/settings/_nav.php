<nav id="project_settings_nav" class="tabs">
	<ul>
		<li<?php echo iif(active_nav('/:slug/settings'), ' class="active"')?>><?php echo HTML::link(l('information'), "{$project->slug}/settings"); ?></li>
		<li<?php echo iif(active_nav('/:slug/settings/milestones(.*)'), ' class="active"')?>><?php echo HTML::link(l('milestones'), "{$project->slug}/settings/milestones"); ?></li>
		<li<?php echo iif(active_nav('/:slug/settings/components(.*)'), ' class="active"')?>><?php echo HTML::link(l('components'), "{$project->slug}/settings/components"); ?></li>
		<li<?php echo iif(active_nav('/:slug/settings/repositories(.*)'), ' class="active"')?>><?php echo HTML::link(l('repositories'), "{$project->slug}/settings/repositories"); ?></li>
	</ul>
</nav>