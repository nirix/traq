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
})

jQuery(document).ready(function () {
  // Add a click event to all elements with
  // a data-popover-hover attribute.
  jQuery(document).on('mouseenter', '[data-popover-hover]', function () {
    var parent = jQuery(this)
    jQuery('#popover')
      .stop(true, true)
      .hide()
      .load(jQuery(this).attr('data-popover-hover') + '?popover=true', function () {
        jQuery('#popover').popover(parent, 'hover')
      })
    parent.off('click').click(function () {
      return false
    })
  })

  // Move ticket form refresh
  jQuery('form#move_ticket #project_id').change(function () {
    jQuery('form#move_ticket input:hidden[name=step]').val(2)
    jQuery('form#move_ticket').submit()
  })
})

/*!
 * jQuery Overlay
 * Copyright (c) 2011-2012 Jack Polgar
 * All Rights Reserved
 * Released under the BSD 3-clause license.
 */
;(function ($) {
  $.fn.overlay = function () {
    var element = jQuery(this)
    element.fadeIn()
    element.css({ left: jQuery(window).width() / 2 - element.width() / 2, top: '18%' })
    jQuery('#overlay_blackout').css({
      display: 'none',
      opacity: 0.7,
      position: 'fixed',
      width: jQuery(document).width() + 100,
      height: jQuery(document).height() + 100,
      top: -100 + 'px',
      left: -100 + 'px',
    })
    jQuery('#overlay_blackout').fadeIn('', function () {
      jQuery('#overlay_blackout').bind('click', function () {
        jQuery('#overlay_blackout').fadeOut()
        element.fadeOut()
      })
    })
  }
})(jQuery)
