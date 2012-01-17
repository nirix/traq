<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<?php echo HTML::css_link(Request::base() . 'assets/css/master.css'); ?>
		<?php echo HTML::css_link(Request::base() . 'assets/css/print.css', 'print'); ?>
		<!--[if lt IE 8]>
		<?php echo HTML::css_link(Request::base() . 'assets/css/ie.css'); ?>
		<![endif]-->
	</head>
	<body>
		<div id="wrapper">
			<header id="header">
				<h1><?php echo settings('title'); ?></h1>
			</header>
			<nav id="nav">
				<ul>
				<?php if (isset($traq->project)) { ?>
					<li><a href="<?php echo Request::base($traq->project->slug)?>"><?php echo l('project_info')?></a></li>
					<li><a href="<?php echo Request::base($traq->project->slug, 'tickets')?>"><?php echo l('tickets')?></a></li>
					<li><a href="<?php echo Request::base($traq->project->slug, 'timeline')?>"><?php echo l('timeline')?></a></li>
				<?php } ?>
				</ul>
			</nav>
			<section id="page">
<?php echo $output; ?>
			</section>
			<footer id="footer">
				<?php echo l('copyright'); ?>
			</footer>
		</div>
	</body>
</html>