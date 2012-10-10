/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
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

var traq = {
	editing_ticket: false,
	editing_wiki: false,
	ticket: {
		getTemplate: function() {
			var type_id = $("#ticket_type option:selected").val()
			$("#ticket_body").load(BASE_URL + '_ajax/ticket_template/' + type_id);
		}
	}
}
$(document).ready(function(){
	// Ticket Type Template
	$('#ticket_type').change(function() {
		traq.ticket.getTemplate();
	});

	// Edit Ticket Template
	$('#edit_ticket_content').click(function() {
		if(traq.editing_ticket) { return false; }

		traq.editing_ticket = true;
		var obj = $('#ticket_content');
		var old_ticket_content = obj.html();
		var tiid = $('#ticket_iid').val();

		obj.load(BASE_URL + '_ajax/ticket_content/' + tiid, function() {
			$('#update_ticket_save').click(function() {
				$.post(BASE_URL + '_ajax/ticket_content/' + tiid + '/save', { body: $('#new_ticket_content').val() }, function(data) {
					obj.html(data);
					traq.editing_ticket = false;
				});
			});
			$('#update_ticket_cancel').click(function() { obj.html(old_ticket_content); traq.editing_ticket = false; });
		});
	});

	// Edit Wiki Page
	$('#edit_wiki_page').click(function() {
		if(traq.editing_wiki) { return false; }

		traq.editing_wiki = true;
		var obj = $('#wiki_page');
		var old_wiki_content = obj.html();
		var wikident = $('#wikident').val();

		obj.load(BASE_URL + '_ajax/wiki_content/' + wikident, function() {
			$('#update_wiki_save').click(function() {
				$.post(BASE_URL + '_ajax/wiki_content/' + wikident + '/save', { body: $('#new_wiki_content').val() }, function(data) {
					obj.html(data);
					traq.editing_wiki = false;
					window.location.reload();
				});
			});
			$('#update_wiki_cancel').click(function() { obj.html(old_wiki_content); traq.editing_wiki = false; });
		});
	});
});

function do_search() {
	var project_slug = $('#search input[name="project_slug"]').val();
	var query = $('#search input[name="search"]').val();
	window.location.href = BASE_URL + project_slug + "/tickets?summary=" + query + "&description=" + query;
}