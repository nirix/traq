
/*
 * Traq
 * Copyright (C) 2009-2014 Traq.io
 * Copyright (C) 2009-2014 Jack Polgar
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

(function() {
  $(document).ready(function() {
    var chosen_options, doc;
    doc = $(document);
    if ($.cookie('show_ticket_filters') === 'true') {
      $('#ticket-filters-content').show();
    }
    chosen_options = {
      disable_search_threshold: 10
    };
    $('select:not(#new_filter)').chosen(chosen_options);
    doc.on('shown.bs.modal', function() {
      return $('select:not(#new_filter)').chosen(chosen_options);
    });
    doc.on('click', '[data-confirm]', function(event) {
      var href;
      event.preventDefault();
      href = $(this).attr('href');
      return window.traq.popoverConfirm($(this), $(this).attr('data-confirm'), function() {
        return window.location.href = href;
      });
    });
    doc.on('click', '[data-ajax-confirm]', function(event) {
      var href;
      event.preventDefault();
      href = $(this).attr('href') + '.js';
      return window.traq.popoverConfirm($(this), $(this).attr('data-ajax-confirm'), function() {
        return $.ajax({
          url: href,
          dataType: 'script'
        });
      });
    });
    doc.on('click', '[data-ajax=1]', function(event) {
      $.ajax({
        url: $(this).attr('href') + '.js',
        dataType: 'script'
      });
      return event.preventDefault();
    });
    doc.on('focus', '[data-autocomplete]', function() {
      return $(this).autocomplete({
        source: $(this).attr('data-autocomplete')
      });
    });
    doc.on('click', '[data-overlay]', function(event) {
      event.preventDefault();
      return window.traq.overlay($(this));
    });
    doc.on('focus', 'input.datepicker', function() {
      return $(this).datepicker({
        dateFormat: $(this).attr('data-date-format'),
        changeMonth: true,
        changeYear: true
      });
    });
    $('#ticket-filters-toggle').on('click', function(event) {
      event.preventDefault();
      if ($('#ticket-filters-content').css('display') === 'none') {
        $.cookie('show_ticket_filters', true);
      } else {
        $.cookie('show_ticket_filters', false);
      }
      return $('#ticket-filters-content').slideToggle();
    });
    $('#ticketlist-columns-toggle').on('click', function(event) {
      event.preventDefault();
      return $('#ticketlist-columns-content').slideToggle();
    });
    doc.on('click', 'button.remove-filter', function(event) {
      var filterRow;
      event.preventDefault();
      filterRow = $(this).attr('data-filter');
      return $('#filter-' + filterRow).fadeOut(function() {
        return $(this).remove();
      });
    });
    return $('[data-moment]').each(function() {
      var n, orig;
      orig = $(this).attr('data-moment');
      if (orig) {
        n = moment(orig).fromNow();
        $(this).html(n);
        return $(this).attr('title', orig);
      }
    });
  });

}).call(this);
