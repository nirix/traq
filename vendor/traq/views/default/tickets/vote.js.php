<?php if (!$error) { ?>
$('#votes').html('<?php echo $ticket->votes; ?>');
<?php } else { ?>
alert('<?php echo $error; ?>');
<?php } ?>