<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo settings('title')?></title>
		<?php echo HTML::css_inc(baseurl('css/themes/'.settings('theme').'/style.css'))?>
		<?php echo HTML::js_inc('http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js')?>
		<?php echo HTML::js_inc(baseurl('js/traq.js'))?>
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		<div id="wrapper">
			<header id="header">
				<div class="width">
				<h1><?php echo HTML::link(baseurl(), settings('title'))?></h1>
					<nav>
						<ul id="meta_nav">
							<?php if(LOGGEDIN) { ?>
							<li><?php echo HTML::link(baseurl('usercp'), l('usercp'))?></li>
							<li><?php echo HTML::link(baseurl('logout'), l('logout'))?></li>
							<?php } else { ?>
							<li><?php echo HTML::link(baseurl('login'), l('login'))?></li>
							<li><?php echo HTML::link(baseurl('register'), l('register'))?></li>
							<?php } ?>
						</ul>
						<ul>
							<?php if(isset($traq->project['id'])) { ?>
							<li><?php echo HTML::link(baseurl($traq->project['slug']), l('project_info'))?></li>
							<li><?php echo HTML::link(baseurl($traq->project['slug'],'timeline'), l('timeline'))?></li>
							<li><?php echo HTML::link(baseurl($traq->project['slug'],'roadmap'), l('roadmap'))?></li>
							<li><?php echo HTML::link(baseurl($traq->project['slug'],'tickets'), l('tickets'))?></li>
							<?php } else { ?>
							<li><?php echo HTML::link(baseurl(), l('projects'))?></li>
							<?php } ?>
						</ul>
					</nav>
				</div>
			</header>
			<section id="page">
				<div class="width">
					<?php echo $output; ?>
				</div>
			</section>
			<footer id="footer">
				<div class="width">
				<?php _l('powered_by')?>
				</div>
			</footer>
		</div>
	</body>
</html>