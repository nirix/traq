/*!
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
 * 
 * This file is part of Traq.
 * 
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function(){
	// Add the editor interface to all text areas, like a boss.
	$('textarea.editor').likeaboss();
	
	// Add a confirm-on-click event to call elements
	// with the data-confirm attribute.
	$(document).on('click', '[data-confirm]', function(){
		return confirm($(this).attr('data-confirm'));
	});
	
	// Add a click event to all elements with
	// the data-ajax attribute and send an ajax
	// call to the href attrib value.
	$(document).on('click', '[data-ajax=1]', function(){
		var e = $(this);
		$.ajax({
			url: e.attr('href'),
			dataType: 'script'
		});
		return false;
	});
	
	// Add a click even to all elements with the
	// data-overlay attribute and load the elements
	// href value into the overlay container then show
	// the overlay.
	$(document).on('click', '[data-overlay=1]', function(){
		var path = $(this).attr('href').split('?');
		var uri = path[0] + '?overlay=true';

		if (path.length > 1) {
			var uri = uri + '&' + path[1];
		}

		$('#overlay').load(uri, function(){ $('#overlay textarea').likeaboss(); $('#overlay').overlay(); });
		return false;
	});

	// Add a hover event to all abbreviation elements inside
	// a form for sexy tooltips.
	$(document).on({
		mouseenter: function(){
			$(this).sexyTooltip();
		}
	}, 'form abbr');

	$(document).on({
		mouseenter: function(){
			$(this).sexyTooltip('top');
		}
	}, '[title]:not(form abbr),[data-tooltip]:not(form abbr)');

	// Add a click event to all elements with
	// a data-popover attribute.
	$(document).on('click', '[data-popover]', function(){
		var e = $(this);
		popover(e.attr('data-popover') + '?popover=true', e);
		return false;
	});

	// Add a click event to all elements with
	// a data-popover-hover attribute.
	$(document).on('mouseenter', '[data-popover-hover]', function(){
		var e = $(this);
		popover(e.attr('data-popover-hover') + '?popover=true', e, 'hover');
	});
});

/*!
 * jQuery Overlay
 * Copyright (c) 2011-2012 Jack Polgar
 * All Rights Reserved
 * Released under the BSD 3-clause license.
 */
(function($){
	$.fn.overlay = function() {
		var element = $(this);
		element.fadeIn();
		element.css({left: jQuery(window).width() / 2-element.width() / 2, top: '18%'});
		$('#overlay_blackout').css({display: 'none', opacity: 0.7, position: 'fixed', width: jQuery(document).width()+100, height: jQuery(document).height()+100, top: -100 + 'px', left: -100 + 'px' });
		$('#overlay_blackout').fadeIn('', function() {
			$('#overlay_blackout').bind('click', function() {
				$('#overlay_blackout').fadeOut();
				element.fadeOut();
			});
		});
	};
})(jQuery);

// Function to close overlay
function close_overlay()
{
	$('#overlay_blackout').fadeOut();
	$('#overlay').fadeOut();
}