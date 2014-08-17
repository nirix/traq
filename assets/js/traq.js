
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
  window.traq = {
    uri: '',
    loadTicketTemplate: function() {
      var typeId;
      typeId = $("#type_id option:selected").val();
      $("#description").load(window.traq.uri + '/_ajax/ticket_template/' + typeId);
      return window.traq.updateTicketCustomFields();
    },
    updateTicketCustomFields: function() {
      var typeId;
      typeId = $("#type_id option:selected").val();
      return $(".properties .custom_field").each(function() {
        var field;
        field = $(this);
        if (field.hasClass('field-for-type-0') || field.hasClass('field-for-type' + typeId)) {
          field.show();
          return alert('wat');
        } else {
          return field.hide();
        }
      });
    },
    overlay: function(element) {
      var uri;
      if (element.attr('data-overlay') === '1') {
        uri = element.attr('href');
      } else {
        uri = element.attr('data-overlay');
      }
      return $.ajax({
        url: uri,
        type: "GET",
        headers: {
          'x-overlay': true
        },
        success: function(data) {
          var modal;
          $('#overlay').html(data);
          if (element.attr('data-target') != null) {
            modal = $(element.attr('data-target'));
          } else {
            modal = $('#overlay .modal');
          }
          return modal.modal('show');
        }
      });
    },
    popoverConfirm: function(element, message, callback) {
      var content;
      element.on('click', function(e) {
        return e.preventDefault();
      });
      content = '<div class="text-center"><div class="btn-group">' + '<button class="btn btn-sm btn-primary popover-btn-confirm">' + '<i class="fa fa-check"></i> ' + window.traq.locale.confirm.yes + '</button>' + '<button class="btn btn-sm btn-default popover-btn-cancel">' + '<i class="fa fa-times"></i> ' + window.traq.locale.confirm.no + '</button>' + '</div></div>';
      element.popover({
        title: message,
        content: content,
        html: true,
        placement: 'bottom',
        trigger: 'click'
      });
      return $(document).on('shown.bs.popover', function(event) {
        var link, popover;
        link = $(event.target);
        popover = link.next();
        popover.find('.popover-btn-confirm').one('click', function() {
          callback();
          return popover.popover('hide');
        });
        return popover.find('.popover-btn-cancel').one('click', function() {
          return popover.popover('hide');
        });
      });
    }
  };

}).call(this);
