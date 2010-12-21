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

$(document).ready(function(){
	$('#ticket_type').change(function() {
		getTicketTemplate();
	});
	
	$('#ticket_content').dblclick(function() {
		var obj = $('#ticket_content');
		var old_ticket_content = obj.html();
		var tiid = $('#ticket_iid').val();
		
		obj.load(BASE_URL + '_ajax/ticket_content/' + tiid, function() {
			$('#update_ticket_save').click(function() {
				//obj.load(BASE_URL + '_ajax/ticket_content' + tiid + '/save');
				$.post(BASE_URL + '_ajax/ticket_content/' + tiid + '/save', { body: $('#new_ticket_content').val() }, function(data) {
					obj.html(data)
				});
			});
			$('#update_ticket_cancel').click(function() { obj.html(old_ticket_content); });
		});
	});
});

function getTicketTemplate()
{
	var type_id = $("#ticket_type option:selected").val()
	$("#ticket_body").load(BASE_URL + '_ajax/ticket_template/' + type_id);
}