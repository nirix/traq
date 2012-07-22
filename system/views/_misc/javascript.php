traq.base = '<?php echo Request::base(); ?>';
<?php foreach ($strings as $key => $value) { ?>
likeABoss.strings['<?php echo $key; ?>'] = '<?php echo $value; ?>';
<?php } ?>
