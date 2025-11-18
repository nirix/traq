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
import type { TicketInterface } from '../interfaces'

Alpine.data('profileTickets', ({ userName, projectSlug }) => ({
    userName: userName,
    projectSlug: projectSlug,
    tickets: [] as TicketInterface[],
    isLoading: false,
    page: 1,
    totalPages: 1,
    open: true,

    init() {
        this.getTickets(this.page)

        this.$watch('page', (newPage) => {
            this.getTickets(newPage)
        })
    },

    getTickets(page: number) {
        this.isLoading = true
        return axios.get(window.traq.base + this.projectSlug + '/tickets.json?assigned_to=' + this.userName + '&page=' + page).then(response => {
            this.tickets = response.data.tickets
            this.totalPages = response.data.total_pages
            this.page = response.data.page
            this.isLoading = false
        })
    },
}))
