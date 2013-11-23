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
	// Manage ticket tasks
	$(document).on('click', '#manage_ticket_tasks', function(){
		$('#overlay').load($(this).attr('data-url') + '?overlay=true', function(){
			var tasks = $('#ticket_tasks_data input[name="tasks"]').val();

			if (tasks == '') {
				tasks = new Array();
			} else {
				tasks = JSON.parse(tasks);
			}

			$(tasks).each(function(task_id, task){
				$("#ticket_tasks_manager #task_count").val(tasks.length);
				$.get(
					traq.base + '_misc/ticket_tasks_bit',
					{ id: task_id, completed: String(task.completed), task: task.task },
					function(data){
						$('#ticket_tasks_manager .tasks').append(data);
					}
				);
			});
			$('#overlay').overlay();
		});
	});

	// Add ticket task
	$(document).on('click', "#ticket_tasks_manager #add_task", function(){
		var task_id = parseInt($("#task_count").val());
		$.get(traq.base + '_misc/ticket_tasks_bit?id=' + task_id, function(data){
			$("#task_count").val(task_id += 1);
			$("#ticket_tasks_manager .tasks").append($(data).hide()).find('.task:last').slideDown('fast', function(){
				$(this).find('[type=text]').focus();
			});
		});
	});

	// Process ticket tasks form data
	$(document).on('click', "#overlay #set_ticket_tasks", function(){
		close_overlay(function(){
			var task_count = parseInt($("#task_count").val());
			var data = new Array();
			$('#ticket_tasks_manager input[name*="tasks"]').each(function(){
				var e = $(this);
				var task_id = parseInt(e.attr('data-task-id'));

				if (!data[task_id]) {
					data[task_id] = {}
				}

				// Checkbox
				if (e.attr('type') == 'checkbox') {
					if (e.is(':checked')) {
						data[task_id].completed = true;
					} else {
						data[task_id].completed = false;
					}
				}
				// Text
				else if(e.attr('type') == 'text') {
					data[task_id].task = e.val();
				}
			});
			$("#ticket_tasks_data input[name='task_count']").val(task_count);
			$("#ticket_tasks_data input[name='tasks']").val(JSON.stringify(data));
		});
	});

	// Delete ticket task
	$(document).on('click', '#overlay button.delete_ticket_task', function(){
		var e = $(this);
		$("#overlay #ticket_task_bit_" + e.attr('data-task-id')).slideUp('fast', function(){
			$(this).remove();
		});
	});

	// Toggle task state
	$(document).on('click', '#ticket_info #tasks .task input[type="checkbox"]', function(){
		var task_id = $(this).attr('data-task-task');
		var completed = false;

		// Get task state
		if ($(this).is(':checked')) {
			completed = true;
		}

		// Update task
		$.ajax({
			url: $(this).attr('data-url'),
			data: { completed: completed },
			beforeSend: function(){
				// Disable tasks
				$('#tasks input[type="checkbox"]').each(function(){
					$(this).attr('disabled','disabled');
				});
			}
		}).done(function(){
			// Enable tasks
			$('#tasks input[type="checkbox"]').each(function(){
				$(this).removeAttr('disabled');
			});
		});
	});
});
