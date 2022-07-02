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
  // I'm not particularly proud of the code below, but then again I'm not at all
  // proud of the 3.x codebase, so screw it, I'll make it better in 4.x.

  jQuery("#select_all_users").on("click", function () {
    if (jQuery(this).is(":checked")) {
      jQuery("#users .mass_actions input").each(function () {
        jQuery(this).prop("checked", true)
      })

      // jQuery('#mass_actions').slideDown('fast');
    } else {
      jQuery("#users .mass_actions input").each(function () {
        jQuery(this).prop("checked", false)
      })

      // jQuery('#mass_actions').slideUp('fast');
    }
  })

  jQuery("#users .mass_actions input").each(function () {
    jQuery(this).on("click", function () {
      if (jQuery("#users .mass_actions input:checked").length > 0) {
        jQuery("#mass_actions").slideDown("fast")
      } else {
        jQuery("#mass_actions").slideUp("fast")
      }
    })
  })
})
