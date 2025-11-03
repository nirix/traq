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
  page: 1,
  totalPages: 1,
  isLoading: false,
  showColumnSettings: true,
  showFilters: true,
  sortColumn: null as string | null,
  sortOrder: 'asc',

  init() {
    this.page = parseInt((new URLSearchParams(window.location.search)).get('page') || '1', 10) || 1;

    this.fetchTickets();
  },

  fetchUrl() {
    let url = `${window.traq.base}${window.traq.project_slug}/tickets.json`;

    const params = new URLSearchParams(window.location.search);

    // if (this.sortColumn) {
    //   url += `?order_by=${this.sortColumn}.${this.sortOrder}`;
    // }
    params.set('page', this.page.toString());
    if (this.sortColumn) {
      params.set('order_by', `${this.sortColumn}.${this.sortOrder}`);
    }

    const paramString = params.toString();
    if (paramString) {
      url += `?${paramString}`;
    }

    return url;
  },

  // 4. Add your methods
  fetchTickets() {
    this.isLoading = true;
    axios.get(this.fetchUrl())
      .then(response => {
        this.tickets = response.data.tickets;
        this.page = response.data.page;
        this.totalPages = response.data.total_pages;
        this.isLoading = false;
      })
      .catch(err => {
        console.error(err);
        this.isLoading = false;
      });
  },

  sort(column: string) {
    if (this.sortColumn === column) {
      this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
    }

    this.sortColumn = column;

    this.fetchTickets();
  },

  prevPageUrl() {
    if (this.page <= 1) {
      return '#';
    }

    return `${window.traq.base}${window.traq.project_slug}/tickets?page=${this.page - 1}`;
  },
  nextPageUrl() {
    if (this.page >= this.totalPages) {
      return '#';
    }

    return `${window.traq.base}${window.traq.project_slug}/tickets?page=${this.page + 1}`;
  },
  prevPage() {
    if (this.page <= 1) {
      return;
    }

    this.page--;
    this.fetchTickets();
  },
  nextPage() {
    if (this.page >= this.totalPages) {
      return;
    }

    this.page++;
    this.fetchTickets();
  },
}));
