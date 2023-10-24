/*!
 * Traq
 * Copyright (C) 2009-2022 Jack Polgar
 * Copyright (C) 2012-2022 Traq.io
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
import type { TicketInterface, MilestoneInterface } from '../interfaces'

Alpine.data('navbarSearch', () => ({
  open: false,
  hasResults: false,
  global: false,
  results: {},

  fetchResults() {
    this.global = window.traq.project_slug ? false : true

    const data = {
      query: this.$refs.input.value,
      project: window.traq.project_slug,
    }

    axios.post(`${window.traq.base}api/search`, data).then((resp) => {
      this.results = resp.data ?? {}
      this.hasResults = this.results.milestones?.length > 0 || this.results.tickets?.length > 0
    })
  },

  ticketUrl(ticket: TicketInterface) {
    return `${window.traq.base}${ticket.project.slug}/tickets/${ticket.ticket_id}`
  },

  milestoneUrl(milestone: MilestoneInterface) {
    return `${window.traq.base}${milestone.project.slug}/milestone/${milestone.slug}`
  },
}))
