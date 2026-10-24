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
  show_hide_custom_fields: function () {
    var type_id = jQuery('#type option:selected').val()

    // Toggle visibility for custom fields that aren't relveant
    // for the selected type.
    jQuery('.properties .custom_field').each(function () {
      var field = jQuery(this)

      // Hide the field
      field.hide()

      // Check if it is for 0 (all) or the selected type ID and show it.
      if (field.hasClass('field-for-type-0') || field.hasClass('field-for-type-' + type_id)) {
        field.show()
      }
    })
  },
}

// Language object
var language = {}

jQuery(document).ready(function () {
  // Add a click even to all elements with the
  // data-overlay attribute and load the elements
  // href value into the overlay container then show
  // the overlay.
  jQuery(document).on('click', '[data-overlay]', function () {
    var path

    if (jQuery(this).attr('data-overlay') == '1') {
      path = jQuery(this).attr('href').split('?')
    } else {
      path = jQuery(this).attr('data-overlay').split('?')
    }

    var uri = path[0] + '?overlay=true'

    if (path.length > 1) {
      var uri = uri + '&' + path[1]
    }

    jQuery('#overlay').load(uri, function () {
      window.Alpine.start()
      jQuery('#overlay').overlay()
    })
    return false
  })

  // Add a hover event to all abbreviation elements inside
  // a form for sexy tooltips.
  jQuery(document).on(
    {
      mouseenter: function () {
        jQuery(this).sexyTooltip()
      },
    },
    'form abbr',
  )

  jQuery(document).on(
    {
      mouseenter: function () {
        jQuery(this).sexyTooltip('top')
      },
    },
    '[title]:not(form abbr),[data-tooltip]:not(form abbr)',
  )

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

  // Loopover all the inputs with an autocomplete attribute
  // and set them up with the source as the attribute value.
  jQuery('input[data-autocomplete]').each(function () {
    jQuery(this).autocomplete({ source: jQuery(this).attr('data-autocomplete') })
  })

  // Move ticket form refresh
  jQuery('form#move_ticket #project_id').change(function () {
    jQuery('form#move_ticket input:hidden[name=step]').val(2)
    jQuery('form#move_ticket').submit()
  })

  // Datepicker
  jQuery(document).on(
    {
      mouseenter: function () {
        jQuery(this).datepicker({
          dateFormat: jQuery(this).attr('data-date-format'),
          changeMonth: true,
          changeYear: true,
        })
      },
    },
    'input.datepicker',
  )
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

// Function to close overlay
function close_overlay(func) {
  if (func == undefined) {
    func = function () {}
  }

  jQuery('#overlay_blackout').fadeOut()
  jQuery('#overlay').fadeOut(function () {
    func()
  })
}
