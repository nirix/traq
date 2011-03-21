<!DOCTYPE html>
<html>
	<head>
	
	</head>
	<body>
		<div id="wrapper">
			<header>
				<?php echo settings('title')?>
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