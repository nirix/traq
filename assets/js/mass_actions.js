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
	var selected_tickets = [];

	$('.mass_actions #select_all_tickets').on('click', function(){
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
		});
	});

	// Loop over checkboxes
	$('.mass_actions input[type="checkbox"][name^="tickets"]').each(function(){
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
		});
	});
});
