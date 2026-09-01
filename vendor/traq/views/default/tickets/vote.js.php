<?php if (!$error) { ?>
$('#votes').html('<?php echo $ticket->votes; ?>');
<?php } else { ?>
alert(<?= js($error) ?>);
<?php } ?>