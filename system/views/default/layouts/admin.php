<!DOCTYPE html>
<html>
	<head>
		<?php View::render('layouts/_head'); ?>
		<?php FishHook::run('template:layouts/admin/head'); ?>
	</head>
	<body>
		<div id="overlay_blackout"></div>
		<div id="overlay"></div>
		<div id="popover"></div>
		<div id="wrapper" class="container">
			<?php echo View::render('layouts/_meta_nav'); ?>
			<header id="header">
				<h1><?php echo HTML::link(l('admincp'), '/admin'); ?></h1>
			</header>
			<nav id="nav">
				<ul id="main_nav">
					<li<?php echo iif(active_nav('/admin/settings?'), ' class="active"')?>><?php echo HTML::link(l('settings'), "/admin/settings"); ?></li>
					<li<?php echo iif(active_nav('/admin(?:/projects(.*))?'), ' class="active"')?>><?php echo HTML::link(l('projects'), "/admin"); ?></li>
					<li<?php echo iif(active_nav('/admin/roles(.*)'), ' class="active"')?>><?php echo HTML::link(l('roles'), "/admin/roles"); ?></li>
					<li<?php echo iif(active_nav('/admin/users(.*)'), ' class="active"')?>><?php echo HTML::link(l('users'), "/admin/users"); ?></li>
					<li<?php echo iif(active_nav('/admin/groups(.*)'), ' class="active"')?>><?php echo HTML::link(l('groups'), "/admin/groups"); ?></li>
					<li<?php echo iif(active_nav('/admin/plugins(.*)'), ' class="active"')?>><?php echo HTML::link(l('plugins'), "/admin/plugins"); ?></li>
					<li<?php echo iif(active_nav('/admin/tickets/types(.*)'), ' class="active"')?>><?php echo HTML::link(l('types'), "/admin/tickets/types"); ?></li>
					<li<?php echo iif(active_nav('/admin/tickets/statuses(.*)'), ' class="active"')?>><?php echo HTML::link(l('statuses'), "/admin/tickets/statuses"); ?></li>
					<li<?php echo iif(active_nav('/admin/priorities(.*)'), ' class="active"')?>><?php echo HTML::link(l('priorities'), "/admin/priorities"); ?></li>
					<li<?php echo iif(active_nav('/admin/severities(.*)'), ' class="active"')?>><?php echo HTML::link(l('severities'), "/admin/severities"); ?></li>
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