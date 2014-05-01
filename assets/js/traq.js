/*!
 * Traq
 * Copyright (C) 2009-2014 Traq.io
 * Copyright (C) 2009-2014 Jack P.
 * https://github.com/nirix
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

// The main Traq object
var traq = {
	base: '/',
	load_ticket_template: function(){
		var type_id = $("#type option:selected").val();

		$("#description").load(traq.base + '_ajax/ticket_template/' + type_id);

		traq.show_hide_custom_fields()
	},
	show_hide_custom_fields: function(){
		var type_id = $("#type option:selected").val();

		// Toggle visibility for custom fields that aren't relveant
		// for the selected type.
		$(".properties .custom_field").each(function(){
			var field = $(this);

			// Hide the field
			field.hide();

			// Check if it is for 0 (all) or the selected type ID and show it.
			if (field.hasClass('field-for-type-0') || field.hasClass('field-for-type-' + type_id)) {
				field.show();
			}
		});
	}
};

// Cookies, nom nom nom
$.cookie.defaults.path = '/';

// Language object
var language = {};

// Instead of that annoying popup confirm box
// how about a nice simple popover box.
// Credit to arturo182
var popover_confirm = function(parent, message, callback) {
	var outerDiv = $('<div/>').addClass('popover_confirm');
	var innerDiv = $('<div/>');

	innerDiv.append($('<button/>', { 'text' : language.yes }).click(function(){
		$("#popover").fadeOut('fast');
		callback();
		return false;
	}));
	innerDiv.append($('<button/>', { 'text' : language.no }).click(function(){ $("#popover").fadeOut('fast'); return false; }));

	outerDiv.append(message);
	outerDiv.append(innerDiv);

	$("#popover").stop(true, true).hide().empty().append(outerDiv);
	$("#popover").popover(parent);
}

$(document).ready(function(){
	$("#header h1").on('mouseenter', function(){
		$("#project_switcher_btn").stop(true, true).fadeIn('fast');
		$(this).off('mouseleave').on('mouseleave', function(){
			$("#project_switcher_btn").stop(true, true).fadeOut('fast');
		});
	});

	$("#project_switcher_btn").on('mouseenter', function(){
		$(this).stop(true, true).show();
		$(this).off('mouseleave').on('mouseleave', function(){
			$(this).stop(true, true).fadeOut('fast');
		});
	});

	$("#project_switcher_btn").on('click', function(){
		$(this).off('mouseleave');
		$(".project_switcher").popover($(this));
		return false;
	});

	$(document).on('click', function(){
		$("#project_switcher_btn").stop(true, true).fadeOut('fast');
	});

	$('[data-preview]').on('click', function(){
		var data = $($(this).attr('data-preview')).val();
		$('#overlay').load(traq.base + '_misc/preview_text', { data: data }, function(){
			$('#overlay').overlay();
		});
	});

	// Add the editor interface to all text areas, like a boss.
	$('textarea.editor').likeaboss();

	// Add a confirm-on-click event to call elements
	// with the data-confirm attribute.
	$(document).on('click', '[data-confirm]', function(){
		var parent = $(this);

		popover_confirm(parent, parent.attr('data-confirm'), function(){
			window.location.href = parent.attr('href');
		});

		return false;
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

	// Add a click event to ajax-confirm elements
	// that will confirm with the specified message
	// then send an ajax request if accepted.
	$(document).on('click', '[data-ajax-confirm]', function(){
		var e = $(this);

		popover_confirm(e, e.attr('data-ajax-confirm'), function(){
			$.ajax({
				url: e.attr('href'),
				dataType: 'script'
			});
		});

		return false;
	});

	// Add a click even to all elements with the
	// data-overlay attribute and load the elements
	// href value into the overlay container then show
	// the overlay.
	$(document).on('click', '[data-overlay]', function(){
		var path;

		if ($(this).attr('data-overlay') == '1') {
			path = $(this).attr('href').split('?');
		} else {
			path = $(this).attr('data-overlay').split('?');
		}

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
		var parent = $(this);
		$("#popover").stop(true, true).hide().load($(this).attr('data-popover') + '?popover=true', function(){
			$("#popover").popover(parent);
		});
		return false;
	});

	// Add a click event to all elements with
	// a data-popover-hover attribute.
	$(document).on('mouseenter', '[data-popover-hover]', function(){
		var parent = $(this);
		$("#popover").stop(true, true).hide().load($(this).attr('data-popover-hover') + '?popover=true', function(){
			$("#popover").popover(parent, 'hover');
		});
		parent.off('click').click(function(){ return false; });
	});

	// Loopover all the inputs with an autocomplete attribute
	// and set them up with the source as the attribute value.
	$("input[data-autocomplete]").each(function(){
		$(this).autocomplete({ source: $(this).attr('data-autocomplete') });
	});

	// Move ticket form refresh
	$("form#move_ticket #project_id").change(function(){
		$("form#move_ticket input:hidden[name=step]").val(2);
		$("form#move_ticket").submit();
	});

	// Datepicker
	$(document).on({
		mouseenter: function(){
			$(this).datepicker({
				dateFormat: $(this).attr('data-date-format'),
				changeMonth: true,
				changeYear: true
			});
		}
	}, 'input.datepicker');

	// Ticket filter remover
	$(document).on('click', '#ticket_filters button.button_delete', function(){
		var filter = $(this).attr('data-filter');
		$('#filter-' + filter).fadeOut('fast', function(){
			$(this).remove();
		});
	});

	// Ticket filter form toggle
	$(document).on('click', '#ticket_filters legend a', function(){
		var filters_table = $('#ticket_filters_content');

		if (filters_table.is(':visible')) {
			filters_table.slideUp('fast');
			$.cookie('hide_filters', 1);
		} else {

			filters_table.slideDown('fast');
			$.cookie('hide_filters', 0);
		}
	});

	// Select all options in ticket filter optgroups when clicked.
	$(document).on('click', '#ticket_filters_content select[multiple] optgroup', function(e){
		e.preventDefault();

		// Make sure this is the actual optgroup being clicked on and no an option
		// inside it.
		if (!$(e.target).is('#ticket_filters_content select[multiple] optgroup')) {
			return;
		}

		// Remove currently selected options unless
		// the `ctrl` key is being held.
		if (!e.ctrlKey && !e.metaKey) {
			$('#ticket_filters_content select[multiple] option').prop('selected', false);
		}

		$(this).find('option').prop('selected', true);
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
function close_overlay(func)
{
	if (func == undefined) {
		func = function(){}
	}

	$('#overlay_blackout').fadeOut();
	$('#overlay').fadeOut(function(){ func(); });
}

// Search box
function do_search() {
	var project_slug = $('#search input[name="project_slug"]').val();
	var query = $('#search input[name="search"]').val();
	window.location.href = traq.base + project_slug + "/tickets?search=" + query;
}
