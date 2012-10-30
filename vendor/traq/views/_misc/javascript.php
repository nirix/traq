traq.base = '<?php echo Request::base(); ?>';
<?php foreach ($editor_strings as $key => $value) { ?>
likeABoss.strings['<?php echo $key; ?>'] = '<?php echo $value; ?>';
<?php } ?>
