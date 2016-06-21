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

jQuery(document).ready ->
    $ = jQuery
    doc = $ document
    body = $

    httpMethodForm = (href, method) ->
        form = $ '<form />'
        form.attr 'id', 'temp-link-method-form'
        form.attr 'action', href
        form.attr 'method', 'post'

        form.append(
            $('<input />')
                .attr('type', 'hidden')
                .attr('name', '_method')
                .attr('value', method)
        )

        form.appendTo 'body'

        return form

    # Show ticket filters form
    if Cookies.get('show_ticket_filters') == 'true'
        $('#ticket-filters-content').show()

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

    # Selectize.js
    $('.better-select').selectize
        plugins: ['remove_button']

    # Navbar tooltips
    $('.navbar [title]').tooltip
        placement: 'bottom'

    # Every other tooltip
    $(':not(.navbar) [title]').tooltip()

    # Sexy text editor in models
    doc.on 'shown.bs.modal', ->
        $('select:not(#new_filter)').chosen chosen_options
        $('.modal .rich-editor').each ->
            new SimpleMDE
                element: $(this)[0]
                indentWithTabs: false

            $('.editor-toolbar [title]').tooltip()

    # Sexy text editors
    $('.rich-editor').each ->
        new SimpleMDE
            element: $(this)[0]
            indentWithTabs: false
            promptURLs: true
            status: false

        $('.editor-toolbar [title]').tooltip()

    # Scroll-to-element
    doc.on 'click', '[data-scroll-to]', (event) ->
        event.preventDefault()
        scrollToElement = $(this).data 'scroll-to'
        $('html, body').animate
            scrollTop: $(scrollToElement).offset().top

    # Confirmations
    doc.on 'click', 'a[data-confirm]', (event) ->
        event.preventDefault()

        element = $ this
        msg = element.data 'confirm'
        href = element.attr 'href'
        method = element.data 'method'

        if confirm msg
            if method && method != 'get'
                form = httpMethodForm href, method
                form.submit()
            else
                window.location.href = href

    # Ajax request with confirmation
    doc.on 'click', 'a[data-ajax-confirm]', (event) ->
        event.preventDefault()

        element = $ this
        msg = element.data 'ajax-confirm'
        method = element.data 'method'
        href = element.attr 'href'

        if confirm msg
            $.ajax
                url: href
                dataType: 'script'
                method: method || 'get'

    # Different HTTP request method
    # Ignore links with `data-ajax-confirm` and `data-confirm` attributes.
    doc.on 'click', 'a[data-method]:not([data-ajax-confirm]):not([data-confirm])', (event) ->
        event.preventDefault()

        element = $ this
        method = element.attr('data-method')
        href = element.attr('href')

        if method != 'get'
            form = httpMethodForm href, method
            form.submit()

    # Remote modals
    doc.on 'click', 'a[data-remote-modal]', (event) ->
        event.preventDefault()

        element = $ this
        target = element.attr 'data-remote-modal'
        href = element.attr 'href'

        $.ajax
          url: href
          type: "GET"
          headers:
            'X-Modal': true
          success: (data) ->
            $(data).appendTo 'body'

            modal = $(target)

            # if btn = $('#modalSubmitBtn')
            #     btn.on 'click', (event) ->
            #         $(target + ' form').submit()

            modal.modal 'show'

            # Remove modal completely when hidden
            $(target).on 'hidden.bs.modal', (event) ->
                event.currentTarget.remove()
