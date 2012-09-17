<!DOCTYPE html>
<html>
	<head>
		<?php View::render('layouts/_head'); ?>
		<?php FishHook::run('template:layouts/default/head'); ?>
	</head>
	<body>
		<div id="overlay_blackout"></div>
		<div id="overlay"></div>
		<div id="popover"></div>
		<div id="wrapper" class="container">
			<?php echo View::render('layouts/_meta_nav'); ?>
			<header id="header">
				<h1>
					<?php if (isset($project)) { echo HTML::link($project->name, $project->slug); ?><?php } else { echo HTML::link(settings('title'), null); } ?></h1>
			</header>
			<nav id="nav">
				<ul id="main_nav">
				<?php if (isset($project)) { ?>
					<li<?php echo iif(active_nav('/:slug'), ' class="active"')?>><?php echo HTML::link(l('project_info'), $project->href()); ?></li>
					<li<?php echo iif(active_nav('/:slug/timeline'), ' class="active"')?>><?php echo HTML::link(l('timeline'), $project->href("timeline")); ?></li>
					<li<?php echo iif(active_nav('/:slug/(roadmap|milestone/(.*))'), ' class="active"')?>><?php echo HTML::link(l('roadmap'), $project->href("roadmap")); ?></li>
					<li<?php echo iif(active_nav('/:slug/tickets(?:/[0-9]+)?'), ' class="active"')?>><?php echo HTML::link(l('tickets'), $project->href("tickets")); ?></li>
					<?php if($current_user->permission($project->id, 'create_tickets')) { ?>
					<li<?php echo iif(active_nav('/:slug/tickets/new(.*)'), ' class="active"')?>><?php echo HTML::link(l('new_ticket'), $project->href('tickets/new')); ?></li>
					<?php } ?>
					<li<?php echo iif(active_nav('/:slug/changelog'), ' class="active"')?>><?php echo HTML::link(l('changelog'), $project->href('changelog')); ?></li>
					<?php if ($project->enable_wiki) { ?>
					<li<?php echo iif(active_nav('/:slug/wiki(.*)'), ' class="active"')?>><?php echo HTML::link(l('wiki'), $project->href("wiki")); ?></li>
					<?php } ?>
					<?php if($current_user->permission($project->id, 'project_settings')) { ?>
					<li<?php echo iif(active_nav('/:slug/settings(.*)'), ' class="active"')?>><?php echo HTML::link(l('settings'), $project->href("settings")); ?></li>
					<?php } ?>
				<?php } else { ?>
					<li<?php echo iif(active_nav('/'), ' class="active"')?>><?php echo HTML::link(l('projects'), null); ?></li>
				<?php } ?>
				<?php FishHook::run('template:layouts/default/main_nav', array(isset($project) ? $project : false)); ?>
				</ul>
			</nav>
			<div id="page">
<?php echo $output; ?>
			</div>
			<footer id="footer">
				<?php echo l('copyright', TRAQ_VER, date("Y")); ?>
			</footer>
			<!-- <?php echo round((microtime(true) - START_TIME), 2); ?>s, <?php echo round((memory_get_peak_usage() - START_MEM) / pow(1024, 2), 3); ?>mb -->
		</div>
	</body>
</html>