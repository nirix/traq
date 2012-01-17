<!DOCTYPE html>
<html>
	<head>
		<title><?php echo implode(' / ', $traq->title); ?></title>
		<meta charset="UTF-8" />
		<?php echo HTML::css_link(Request::base() . 'assets/css/master.css'); ?>
		<?php echo HTML::css_link(Request::base() . 'assets/css/print.css', 'print'); ?>
		<!--[if lt IE 8]>
		<?php echo HTML::css_link(Request::base() . 'assets/css/ie.css'); ?>
		<![endif]-->
	</head>
	<body>
		<div id="wrapper" class="container">
			<header id="header">
				<h1><?php echo HTML::link(settings('title'), null); ?><?php if (isset($project)) { ?> / <?php echo HTML::link($project->name, $project->slug); ?><?php } ?></h1>
			</header>
			<nav id="nav">
				<ul id="meta_nav">
				<?php if (LOGGEDIN) { ?>
					<li><?php echo HTML::link(l('usercp'), '/usercp'); ?></li>
					<li><?php echo HTML::link(l('logout'), '/logout'); ?></li>
					<?php if ($current_user->group->is_admin) { ?>
					<li><?php echo HTML::link(l('admincp'), '/admincp'); ?></li>
					<?php } ?>
				<?php } else { ?>
					<li><?php echo HTML::link(l('login'), '/login'); ?></li>
					<li><?php echo HTML::link(l('register'), '/register'); ?></li>
				<?php } ?>
				</ul>
				<ul id="main_nav">
				<?php if (isset($project)) { ?>
					<li><?php echo HTML::link(l('project_info'), $project->slug); ?></li>
					<li><?php echo HTML::link(l('tickets'), "{$project->slug}/tickets"); ?></li>
					<li><?php echo HTML::link(l('timeline'), "{$project->slug}/timeline"); ?></li>
				<?php } else { ?>
					<li><?php echo HTML::link(l('projects'), null); ?></li>
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