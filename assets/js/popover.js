/*!
 * Popover
 * Copyright (c) 2012 Jack P.
 * All Rights Reserved
 * https://github.com/nirix
 *
 * Released under the BSD 3-clause license
 */
(function($){
	$.fn.popover = function(parent, event) {
		if (!event) {
			var event = 'click';
		}

		var popover = $(this);

		// Reset popover
		parent.off('mouseleave');
		popover.off('click', 'mouseenter', 'mouseleave');

		// Set the position
		popover.css({
			left: ((parent.offset().left + (parent.outerWidth() / 2)) - (popover.outerWidth() / 2)) + 'px',
			top: (parent.offset().top + parent.height()) + 'px',
			height: 'auto'
		});

		// Slide it down
		popover.stop(true, true).slideDown('fast', function(){
			// Click
			if (event == 'click') {
				// Bind a click to the document
				$(document).on('click', function(){
					// Fade it out
					popover.fadeOut('fast');
				}).not(popover);

				// Bind a click to the popover
				popover.on('click', function(e){
					e.stopPropagation();
					// Stop it from fading out
					popover.stop(true, true).show();
				});
			}
			// Hover
			else if (event == 'hover') {
				// Delay the mouse leave event binding for the parent
				parent.delay(2000).mouseleave(function(){
					popover.stop(true, true).fadeOut('fast');
				});

				// Handle the hover of the popover
				popover.hover(
					// Enter
					function(){
						parent.off('mouseleave');
						popover.stop(true, true).show();
					},
					// Leave
					function(){
						parent.off('mouseleave');
						popover.off('hover').stop(true, true).fadeOut('fast');
					}
				);
			}
		});
	}
})(jQuery);