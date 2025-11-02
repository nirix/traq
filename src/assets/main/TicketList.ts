/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
 * https://github.com/nirix
 * http://traq.io
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

import axios from 'axios'
import Alpine from 'alpinejs'

Alpine.data('ticketList', () => ({
  columns: {
    'id': true,
    'summary': true,
    'status': true,
    'owner': true,
    'type': true,
    'component': true,
    'milestone': true,
    'assignee': false,
    'priority': false,
    'severity': false,
    'reported': false,
    'updated': false,
    'votes': false,
  },

  tickets: [],
  isLoading: false,
  showColumnSettings: true,

  init() {
    this.fetchTickets();
  },

  // 4. Add your methods
  fetchTickets() {
    this.isLoading = true;
    axios.get(`${window.traq.base}${window.traq.project_slug}/tickets.json`)
      .then(response => {
        this.tickets = response.data.tickets;
        console.log(response.data.tickets);
        this.isLoading = false;
      })
      .catch(err => {
        console.error(err);
        this.isLoading = false;
      });
  }

}));
