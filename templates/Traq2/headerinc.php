<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="<?php echo THEMEDIR?>/style.css" type="text/css" />
<script src="<?php echo str_replace('index.php/','',$uri->anchor()); ?>jquery.min.js" type="text/javascript"></script>
<script src="<?php echo str_replace('index.php/','',$uri->anchor()); ?>traq.js" type="text/javascript"></script>
<?php ($hook = FishHook::hook('template_headerinc')) ? eval($hook) : false; ?>
<script type="text/javascript">
	var BASE_URL = "<?php echo str_replace('index.php/','',$uri->anchor()); ?>";
</script>