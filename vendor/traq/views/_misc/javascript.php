traq.base = '<?php echo Request::base(); ?>';
<?php foreach ($editor_strings as $key => $value) { ?>
likeABoss.strings['<?php echo $key; ?>'] = '<?php echo $value; ?>';
<?php } ?>

// Yes and No translations
language.yes = '<?php echo l('yes'); ?>';
language.no = '<?php echo l('no'); ?>';
