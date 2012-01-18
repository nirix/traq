<nav id="project_settings_nav" class="tabs">
	<ul>
		<li<?php echo Request::matches("{$project->slug}/settings") ? ' class="active"' : '' ?>><?php echo HTML::link(l('information'), "{$project->slug}/settings"); ?></li>
		<li<?php echo Request::seg(2) == 'milestones' ? ' class="active"' : '' ?>><?php echo HTML::link(l('milestones'), "{$project->slug}/settings/milestones"); ?></li>
		<li<?php echo Request::seg(2) == 'components' ? ' class="active"' : '' ?>><?php echo HTML::link(l('components'), "{$project->slug}/settings/components"); ?></li>
	</ul>
</nav>