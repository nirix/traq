/*!
 * Popover
 * Copyright (c) 2012 Jack Polgar
 * All Rights Reserved
 * Released under the BSD 3-clause license
 */
function popover(url, parent, event)
{
	if (!event) {
		var event = 'click';
	}

	// Make sure the popover container exists...
	if (!$('#popover').length > 0) {
		$('body').append('<div id="popover"></div>');
	}
	// Reset the popover element
	else {
		$('#popover').stop(true, true).hide().unbind('hover').unbind('click');
		parent.stop(true, true).unbind('mouseleave');
	}

	// Load in the popover content
	$('#popover').load(url, function(){
		var e = $(this);

		// Set the position
		e.css({
			left: (parent.position().left - (e.width() / 2)) + 'px',
			top: (parent.position().top + parent.height()) + 'px',
			height: 'auto'
		});

		// Slide it down
		e.stop(true, true).slideDown('fast', function(){
			if (event == 'click') {
				// Bind a click to the document
				$(document).click(function(){
					// Fade it out
					$('#popover').fadeOut('fast');
				});

				// Bind a click to the popover
				$('#popover').click(function(e){
					// Stop it from fading out
					e.stopPropagation();
				});
			} else if (event == 'hover') {
				// Delay the mouse leave event binding for the parent
				setInterval(function(){
					parent.mouseleave(function(){
						$('#popover').stop(true, true).fadeOut('fast');
					});
				}, 3000);

				// Handle the entry/leaving of the popover
				$('#popover').hover(
					function(){
						$(this).stop(true, true).show();
					},
					function(){
						$(this).stop(true, true).fadeOut('fast');
					}
				);
			}
		});
	});
}