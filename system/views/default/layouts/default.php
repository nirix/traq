<!DOCTYPE html>
<html>
	<head>
		<title><?php echo implode(' / ', $traq->title); ?></title>
		<meta charset="UTF-8" />
		<?php echo HTML::css_link(Request::base() . 'css.php?css=master'); ?>
		<?php echo HTML::css_link(Request::base() . 'css.php?css=print', 'print'); ?>
		<!--[if lt IE 8]>
		<?php echo HTML::css_link(Request::base() . 'minify.php?assets/css/ie.css'); ?>
		<![endif]-->
		<?php echo HTML::js_inc('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'); ?>
		<?php echo HTML::js_inc(Request::base() . 'assets/js/likeaboss.js'); ?>
		<?php echo HTML::js_inc(Request::base() . 'assets/js/traq.js'); ?>
	</head>
	<body>
		<div id="wrapper" class="container">
			<header id="header">
				<h1><?php echo HTML::link(settings('title'), null); ?><?php if (isset($project)) { ?> / <?php echo HTML::link($project->name, $project->slug); ?><?php } ?></h1>
			</header>
			<nav id="nav">
				<ul id="meta_nav">
				<?php if (LOGGEDIN) { ?>
					<li<?php echo iif(active_nav('/usercp'), ' class="active"')?>><?php echo HTML::link(l('usercp'), '/usercp'); ?></li>
					<li><?php echo HTML::link(l('logout'), '/logout'); ?></li>
					<?php if ($current_user->group->is_admin) { ?>
					<li<?php echo iif(active_nav('/admin(.*)'), ' class="active"')?>><?php echo HTML::link(l('admincp'), '/admin'); ?></li>
					<?php } ?>
				<?php } else { ?>
					<li<?php echo iif(active_nav('/login'), ' class="active"')?>><?php echo HTML::link(l('login'), '/login'); ?></li>
					<li<?php echo iif(active_nav('/register'), ' class="active"')?>><?php echo HTML::link(l('register'), '/register'); ?></li>
				<?php } ?>
				</ul>
				<ul id="main_nav">
				<?php if (isset($project)) { ?>
					<li<?php echo iif(active_nav('/:slug'), ' class="active"')?>><?php echo HTML::link(l('project_info'), $project->href()); ?></li>
					<li<?php echo iif(active_nav('/:slug/roadmap'), ' class="active"')?>><?php echo HTML::link(l('roadmap'), $project->href("roadmap")); ?></li>
					<li<?php echo iif(active_nav('/:slug/tickets'), ' class="active"')?>><?php echo HTML::link(l('tickets'), $project->href("tickets")); ?></li>
					<li<?php echo iif(active_nav('/:slug/timeline'), ' class="active"')?>><?php echo HTML::link(l('timeline'), $project->href("timeline")); ?></li>
					<?php if($project->is_manager($current_user)) { ?>
					<li<?php echo iif(active_nav('/:slug/settings(.*)'), ' class="active"')?>><?php echo HTML::link(l('settings'), $project->href("settings")); ?></li>
					<?php } ?>
				<?php } else { ?>
					<li<?php echo iif(active_nav('/'), ' class="active"')?>><?php echo HTML::link(l('projects'), null); ?></li>
				<?php } ?>
				</ul>
			</nav>
			<div id="page">
<?php echo $output; ?>
			</div>
			<footer id="footer">
				<?php echo l('copyright'); ?>
			</footer>
		</div>
	</body>
</html>