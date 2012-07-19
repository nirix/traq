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

	// Load in the popover content
	$('#popover').load(url, function(){
		var e = $(this);

		// Set the position
		e.css({
			left: (parent.position().left - (e.width() / 2)) + 'px',
			top: (parent.position().top + parent.height()) + 'px'
		});

		// Slide it down
		e.slideDown('fast', function(){
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
				parent.mouseleave(function(){
					$('#popover').fadeOut('fast');
				});
				$('#popover').hover(
					function(){
						$(this).stop();
					},
					function(e){
						$(this).fadeOut('fast');
					}
				);
			}
		});
	});
}