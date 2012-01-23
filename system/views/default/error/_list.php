<?php if (count($errors)) { ?>
<div class="error">
	<ul>
	<?php foreach ($errors as $error) { ?>
		<li><?php echo $error; ?></li>
	<?php } ?>
	</ul>
</div>
<?php } ?>