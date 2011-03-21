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
				<h1><?php echo HTML::link(baseurl(), settings('title'))?></h1>
				<nav>
					<ul>
						<li><?php echo HTML::link('#', 'test')?></li>
						<li><?php echo HTML::link('#', 'test')?></li>
						<li><?php echo HTML::link('#', 'test')?></li>
					</ul>
				</nav>
			</header>
			<section id="page">
				<?php echo $output; ?>
			</section>
			<footer>
				<?php _l('powered_by')?>
			</footer>
		</div>
	</body>
</html>