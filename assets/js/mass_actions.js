/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
 * Copyright (C) 2009-2013 J. Polgar
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

$(document).ready(function(){
	// Get selected tickets.
	var selected_tickets = $.cookie('selected_tickets');

	// Selected users
	var selected_users = [];

	// If there are none, set empty array.
	if (!selected_tickets) {
		selected_tickets = [];
	} else {
		selected_tickets = JSON.parse($.cookie('selected_tickets'));
	}

	// Set form value
	$('#mass_actions input[name="tickets"]').val(JSON.stringify(selected_tickets));

	// Save selected tickets.
	var saveSelectedTickets = function() {
		$.cookie('selected_tickets', JSON.stringify(selected_tickets));

		// Show mass actions form
		if (selected_tickets.length > 0) {
			$('#mass_actions').slideDown('fast');
		} else {
			$('#mass_actions').slideUp('fast');
		}

		$('#mass_actions input[name="tickets"]').val(JSON.stringify(selected_tickets));
	};

	// Check selected tickets
	$(selected_tickets).each(function(i, ticket_id){
		$('#mass_action_ticket_' + ticket_id).prop('checked', true);
		$('#mass_actions').show();
	});

	$('#tickets .mass_actions #select_all_tickets').on('click', function(){
		var select_all = $(this).is(':checked');
		$('#tickets input[type="checkbox"][name^="tickets"]').each(function(){
			var ticket_id = $(this).val();
			if (select_all && !$(this).is(':checked')) {
				$(this).prop('checked', true);
				selected_tickets.push(ticket_id);
			} else if(!select_all && $(this).is(':checked')) {
				$(this).prop('checked', false);
				selected_tickets = $.grep(selected_tickets, function(a){ return a != ticket_id; });
			}
			saveSelectedTickets();
		});
	});

	// Loop over checkboxes
	$('#tickets .mass_actions input[type="checkbox"][name^="tickets"]').each(function(){
		// Add click event
		$(this).on('click', function(){
			var ticket_id = $(this).val();

			// Add ticket ID to selected tickets
			if ($(this).is(':checked')) {
				selected_tickets.push(ticket_id);
			} else {
				selected_tickets = $.grep(selected_tickets, function(a){ return a != ticket_id; });
				$('#tickets #select_all_tickets').prop('checked', false);
			}
			saveSelectedTickets();
		});
	});

	// I'm not particularly proud of the code below, but then again I'm not at all
	// proud of the 3.x codebase, so screw it, I'll make it better in 4.x.

	$('#select_all_users').on('click', function(){
		if ($(this).is(':checked')) {
			$('#users .mass_actions input').each(function(){
				$(this).prop('checked', true);
			});

			// $('#mass_actions').slideDown('fast');
		} else {
			$('#users .mass_actions input').each(function(){
				$(this).prop('checked', false);
			});

			// $('#mass_actions').slideUp('fast');
		}
	});

	$('#users .mass_actions input').each(function(){
		$(this).on('click', function(){
			if ($('#users .mass_actions input:checked').length > 0) {
				$('#mass_actions').slideDown('fast');
			} else {
				$('#mass_actions').slideUp('fast');
			}
		});
	});
});
