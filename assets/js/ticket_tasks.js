/*!
 * Traq
 * Copyright (C) 2009-2022 Traq.io
 * Copyright (C) 2009-2022 J. Polgar
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

jQuery(document).ready(function () {
  // Manage ticket tasks
  jQuery(document).on("click", "#manage_ticket_tasks", function () {
    jQuery("#overlay").load(jQuery(this).attr("data-url") + "?overlay=true", function () {
      var tasks = jQuery('#ticket_tasks_data input[name="tasks"]').val()

      if (tasks == "") {
        tasks = new Array()
      } else {
        tasks = JSON.parse(tasks)
      }

      jQuery(tasks).each(function (task_id, task) {
        jQuery("#ticket_tasks_manager #task_count").val(tasks.length)
        $.get(traq.base + "_misc/ticket_tasks_bit", { id: task_id, completed: String(task.completed), task: task.task }, function (data) {
          jQuery("#ticket_tasks_manager .tasks").append(data)
        })
      })
      jQuery("#overlay").overlay()
    })
  })

  // Add ticket task
  jQuery(document).on("click", "#ticket_tasks_manager #add_task", function () {
    var task_id = parseInt(jQuery("#task_count").val())
    $.get(traq.base + "_misc/ticket_tasks_bit?id=" + task_id, function (data) {
      jQuery("#task_count").val((task_id += 1))
      jQuery("#ticket_tasks_manager .tasks")
        .append(jQuery(data).hide())
        .find(".task:last")
        .slideDown("fast", function () {
          jQuery(this).find("[type=text]").focus()
        })
    })
  })

  // Process ticket tasks form data
  jQuery(document).on("click", "#overlay #set_ticket_tasks", function () {
    close_overlay(function () {
      var task_count = parseInt(jQuery("#task_count").val())
      var data = new Array()
      jQuery('#ticket_tasks_manager input[name*="tasks"]').each(function () {
        var e = jQuery(this)
        var task_id = parseInt(e.attr("data-task-id"))

        if (!data[task_id]) {
          data[task_id] = {}
        }

        // Checkbox
        if (e.attr("type") == "checkbox") {
          if (e.is(":checked")) {
            data[task_id].completed = true
          } else {
            data[task_id].completed = false
          }
        }
        // Text
        else if (e.attr("type") == "text") {
          data[task_id].task = e.val()
        }
      })
      jQuery("#ticket_tasks_data input[name='task_count']").val(task_count)
      jQuery("#ticket_tasks_data input[name='tasks']").val(JSON.stringify(data))
    })
  })

  // Delete ticket task
  jQuery(document).on("click", "#overlay button.delete_ticket_task", function () {
    var e = jQuery(this)
    jQuery("#overlay #ticket_task_bit_" + e.attr("data-task-id")).slideUp("fast", function () {
      jQuery(this).remove()
    })
  })
})
