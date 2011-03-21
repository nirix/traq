<!DOCTYPE html>
<html>
	<head>
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
							<li><?php echo HTML::link(baseurl('usercp'), l('UserCP'))?></li>
							<li><?php echo HTML::link(baseurl('logout'), l('Logout'))?></li>
							<?php } else { ?>
							<li><?php echo HTML::link(baseurl('login'), l('Login'))?></li>
							<li><?php echo HTML::link(baseurl('register'), l('Register'))?></li>
							<?php } ?>
						</ul>
						<ul>
							<?php if(isset($traq->project['id'])) { ?>
							<li><?php echo HTML::link(baseurl($traq->project['slug']), l('Project Info'))?></li>
							<li><?php echo HTML::link(baseurl($traq->project['slug'],'timeline'), l('Timeline'))?></li>
							<li><?php echo HTML::link(baseurl($traq->project['slug'],'roadmap'), l('Roadmap'))?></li>
							<li><?php echo HTML::link(baseurl($traq->project['slug'],'tickets'), l('Tickets'))?></li>
							<?php } else { ?>
							<li><?php echo HTML::link(baseurl(), l('Projects'))?></li>
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
			<footer>
				<div class="width">
				<?php _l('powered_by')?>
				</div>
			</footer>
		</div>
	</body>
</html>