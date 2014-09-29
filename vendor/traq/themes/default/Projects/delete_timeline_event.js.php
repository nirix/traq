$('.timeline_event_<?php echo $event->id?>').each(function(){ $(this).slideUp('fast', function(){ $(this).remove(); }); });
