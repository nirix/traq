###
# Traq
# Copyright (C) 2009-2016 Jack P.
# Copyright (C) 2012-2016 Traq.io
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
###

$(document).ready ->
  doc = $(document)

  # Show ticket filters form
  if Cookies.get('show_ticket_filters') == 'true'
    $('#ticket-filters-content').show()

  # Sexy selects
  chosen_options = {
    disable_search_threshold: 10
  }

  $('select:not(#new_filter)').chosen chosen_options

  doc.on 'shown.bs.modal', ->
    $('select:not(#new_filter)').chosen chosen_options

  # Popover confirmation
  # $('[data-confirm]').each ->/
  doc.on 'click', '[data-confirm]', (event) ->
    event.preventDefault()

    href = $(this).attr('href')
    window.traq.popoverConfirm $(this), $(this).attr('data-confirm'), ->
      window.location.href = href

  # Popover confirmation for remote action
  doc.on 'click', '[data-ajax-confirm]', (event) ->
    event.preventDefault()

    href = $(this).attr('href') + '.js'
    window.traq.popoverConfirm $(this), $(this).attr('data-ajax-confirm'), ->
      $.ajax url: href, dataType: 'script'

  # Ajax based on elements `href` attribute
  doc.on 'click', '[data-ajax=1]', (event) ->
    $.ajax url: $(this).attr('href') + '.js', dataType: 'script'
    event.preventDefault()

  # Autocomplete
  doc.on 'focus', '[data-autocomplete]', ->
    $(this).autocomplete source: $(this).attr('data-autocomplete')

  # Overlay
  doc.on 'click', '[data-overlay]', (event) ->
    event.preventDefault()
    window.traq.overlay $(this)

  # Datepicker
  doc.on 'focus', 'input.datepicker', ->
    $(this).datepicker
      dateFormat: $(this).attr('data-date-format')
      changeMonth: true
      changeYear: true

  # Ticket filters form toggle
  $('#ticket-filters-toggle').on 'click', (event) ->
    event.preventDefault()

    if $('#ticket-filters-content').css('display') == 'none'
      Cookies.set('show_ticket_filters', true)
    else
      Cookies.set('show_ticket_filters', false)

    $('#ticket-filters-content').slideToggle()

  # Ticket listing columns form toggle
  $('#ticketlist-columns-toggle').on 'click', (event) ->
    event.preventDefault()
    $('#ticketlist-columns-content').slideToggle()

  # Remove ticket filter
  doc.on 'click', '.remove-filter', (event) ->
    event.preventDefault()
    filterRow = $(this).attr('data-filter')
    $('#filter-' + filterRow).fadeOut ->
      $(this).remove()

  dataMoment = ->
    $('[data-moment]').each ->
      orig = $(this).attr 'data-moment'

      if orig
        n = moment(orig).fromNow()
        $(this).html n
        $(this).attr 'title', orig

  dataMoment()
  setInterval dataMoment, 30000
