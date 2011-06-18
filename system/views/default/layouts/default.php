<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<?php echo HTML::cssless_inc(Request::base() . 'assets/css/master.less'); ?>
	</head>
	<body>
		<div id="wrapper">
			<header id="header">
				<h1><?php echo settings('title'); ?></h1>
			</header>
			<nav id="nav">
				<ul>
					
				</ul>
			</nav>
			<section id="page">
<?php echo $output; ?>
			</section>
			<footer id="footer">
				
			</footer>
		</div>
	</body>
</html>