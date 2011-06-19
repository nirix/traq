<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<?php echo HTML::cssless_inc(Request::base() . 'assets/css/master.less'); ?>
		<?php echo HTML::css_inc(Request::base() . 'assets/css/print.css', 'print'); ?>
		<!--[if lt IE 8]>
		<?php echo HTML::css_inc(Request::base() . 'assets/css/ie.css'); ?>
		<![endif]-->
		<?php echo HTML::js_inc(Request::base() . 'assets/js/less.js'); ?>
	</head>
	<body>
		<div id="wrapper">
			<header id="header">
				<h1><?php echo settings('title'); ?></h1>
			</header>
			<nav id="nav">
				<ul>
					<li><a href="#">test</a></li>
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