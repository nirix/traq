/*!
 * Traq
 * Copyright (C) 2009-2026 Traq.io
 * Copyright (C) 2009-2026 Jack P.
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
  toggleCustomFieldsVisibility: function () {
    const typeId = document.querySelector('#type').value
    const customFields = document.querySelectorAll('.custom-field')

    customFields.forEach((field) => {
      field.style.display = 'none'

      if (field.classList.contains('field-for-type-0') || field.classList.contains('field-for-type-' + typeId)) {
        field.style.display = 'flex'
      }
    })
  },
}

// Language object
var language = {}

document.addEventListener('DOMContentLoaded', () => {
  // If we're on a ticket page, toggle the custom fields visibility
  if (window.location.pathname.match(/\/tickets\/(\d+|new)$/)) {
    traq.toggleCustomFieldsVisibility()
    document.querySelector('#type').addEventListener('change', () => {
      traq.toggleCustomFieldsVisibility()
    })
  }

  // If we're on the move ticket page, listen for the project change
  if (window.location.pathname.match(/\/tickets\/\d+\/move$/)) {
    document.querySelector('form#move_ticket #project_id').addEventListener('change', () => {
      document.querySelector('form#move_ticket').submit()
    })
  }
})
