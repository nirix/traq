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
import type { StatusInterface, TicketInterface, TypeInterface } from '../interfaces'

Alpine.data('profileTickets', ({ userName, projectSlug }) => ({
    userName: userName,
    projectSlug: projectSlug,
    tickets: [] as TicketInterface[],
    isLoading: false,
    page: 1,
    totalPages: 1,
    open: true,
    sortColumn: 'status',
    sortOrder: 'asc',
    type: [] as string[],
    status: [] as string[],
    statuses: [] as StatusInterface[],
    types: [] as TypeInterface[],

    init() {
        this.getTickets()

        Promise.all([
            axios.get(window.traq.base + 'api/types'),
            axios.get(window.traq.base + 'api/statuses'),
        ]).then(([types, statuses]) => {
            this.types = types.data
            this.statuses = statuses.data
        })

        this.$watch('page', () => {
            this.getTickets()
        })

        this.$watch('status', () => {
            this.getTickets()
        })

        this.$watch('type', () => {
            this.getTickets()
        })
    },

    getTickets() {
        this.isLoading = true

        let url = window.traq.base + this.projectSlug + '/tickets.json?assigned_to=' + this.userName + '&page=' + this.page + '&order_by=' + this.sortColumn + '.' + this.sortOrder

        if (this.status.length > 0) {
            url += '&status=' + this.status.join(',')
        }

        if (this.type.length > 0) {
            url += '&type=' + this.type.join(',')
        }

        return axios.get(url).then(response => {
            this.tickets = response.data.tickets
            this.totalPages = response.data.total_pages
            this.page = response.data.page
            this.isLoading = false
        })
    },

    sortBy(column: string) {
        if (this.sortColumn === column) {
            this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
        }

        this.sortColumn = column

        this.getTickets()
    },
}))
