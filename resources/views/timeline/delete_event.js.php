$('.timeline_event_<?=$event->id?>').each(function(){
    $(this).slideUp('fast', function(){
        $(this).remove();
    });
});
