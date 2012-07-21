<?php if ($config_created) { ?>
<div align="center">Installation Completed</div>
<?php } else { ?>
<div align="center">
	The installer was unable to create the config file,
	You will need to save the code below to: <code>system/config/database.php</code>
</div>
<br />
<pre id="config_code">
<?php echo htmlentities($config_code); ?>
</pre>
<?php } ?>