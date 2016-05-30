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

    # Sexy text editors
    $('.rich-editor').each ->
        new SimpleMDE
            element: $(this)[0]
            indentWithTabs: false

    # Scroll-to-element
    doc.on 'click', '[data-scroll-to]', (event) ->
        event.preventDefault()
        scrollToElement = $(this).data 'scroll-to'
        $('html, body').animate
            scrollTop: $(scrollToElement).offset().top

    # Confirmations
    doc.on 'click', 'a[data-confirm]:not([data-method])', (event) ->
        event.preventDefault()

        element = $ this
        msg = element.attr('data-confirm')
        href = element.attr('href')

        if confirm msg
            window.location.href = href

    # Different HTTP request method
    doc.on 'click', 'a[data-method]', (event) ->
        event.preventDefault()

        element = $ this
        method = element.attr('data-method')
        href = element.attr('href')

        if method != 'get'
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

            if msg = element.attr 'data-confirm'
                if confirm msg
                    $('#temp-link-method-form').submit()
            else
                $('#temp-link-method-form').submit()

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
            'X-Overlay': true
          success: (data) ->
            $(data).appendTo 'body'

            modal = $(target)

            if btn = $('#modalSubmitBtn')
                btn.on 'click', (event) ->
                    $(target + ' form').submit()

            modal.modal 'show'

            # Remove modal completely when hidden
            $(target).on 'hidden.bs.modal', (event) ->
                event.currentTarget.remove()
