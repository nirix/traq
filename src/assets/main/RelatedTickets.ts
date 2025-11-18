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
import type { TicketInterface } from './../interfaces'
interface RelatedTicket {
  id: number
  ticket_id: number
  summary: string
  relation_type_id: number
  priority_id: number
  project_id: number
  project_slug: string
}

interface RelationType {
  id: number
  name: string
  inverse_type_id: number
}

interface NewRelation {
  summary: string
  relation_type_id: number
}

Alpine.data('relatedTickets', ({ ticketId, canEdit }: { ticketId: number, canEdit: boolean }) => ({
  url: '',
  ticketId,
  relatedTickets: [] as RelatedTicket[],
  relationTypes: [] as RelationType[],
  originalRelations: [] as RelatedTicket[],
  // newRelations: [] as NewRelation[],
  newRelation: {
    summary: '',
    relation_type_id: 1,
  } as NewRelation,
  changed: false,
  open: true,
  canEdit,
  searchResults: [],
  activeNewRelationId: null as number | null,

  init() {
    this.url = window.traq.base + window.traq.project_slug + "/tickets/" + this.ticketId + "/related-tickets"

    axios.get(this.url).then((resp) => {
      this.relationTypes = resp.data.relation_types
      this.relatedTickets = resp.data.related_tickets.map((relatedTicket: RelatedTicket) => ({ ...relatedTicket }))
      this.originalRelations = resp.data.related_tickets.map((relatedTicket: RelatedTicket) => ({ ...relatedTicket }))
    })

    // compare originalRelations with relatedTickets and if they are different, update the relatedTickets
    this.$watch('relatedTickets', (relatedTickets) => {
      this.changed = JSON.stringify(this.originalRelations) !== JSON.stringify(relatedTickets)
    })
  },

  ticketHref(relatedTicket: RelatedTicket) {
    return window.traq.base + relatedTicket.project_slug + "/tickets/" + relatedTicket.ticket_id
  },

  remove(relatedTicket: RelatedTicket) {
    this.relatedTickets = this.relatedTickets.filter((relation) => relation.id !== relatedTicket.id)
  },

  save() {
    axios.post(this.url, {
      related_tickets: this.relatedTickets,
    }).then((resp) => {
      this.relatedTickets = resp.data.related_tickets.map((relatedTicket: RelatedTicket) => ({ ...relatedTicket }))
      this.originalRelations = resp.data.related_tickets.map((relatedTicket: RelatedTicket) => ({ ...relatedTicket }))
      this.changed = false
    })
  },

  cancel() {
    this.relatedTickets = this.originalRelations.map((originalRelation) => {
      return {
        ...originalRelation,
      }
    })
  },

  blurSummary(target: HTMLElement) {
    // Check if we clicked into the search results
    if (target.closest('.related-tickets-search-results')) {
      return
    }

    this.searchResults = []
  },

  searchTicket(element: HTMLInputElement) {
    axios.get(window.traq.base + window.traq.project_slug + "/tickets.json?summary=" + this.newRelation.summary).then((resp) => {
      // Filter out tickets already in relatedTickets
      this.searchResults = resp.data.tickets
        .filter((ticket: TicketInterface) => {
          return !this.relatedTickets.some((relatedTicket: RelatedTicket) => relatedTicket.ticket_id === ticket.ticket_id || relatedTicket.ticket_id === ticket.ticket_id)
        })
        .filter((ticket: TicketInterface) => ticket.ticket_id !== this.ticketId)

      // set x/y to position of focusedSummary, taking into account scroll position
      this.$refs.searchResults.style.top = element.getBoundingClientRect().bottom + window.scrollY + 'px'
      this.$refs.searchResults.style.left = element.getBoundingClientRect().left + window.scrollX + 'px'
    })
  },

  selectTicket(searchResult: TicketInterface) {
    const newRelation = {
      id: null,
      ticket_id: searchResult.ticket_id,
      summary: searchResult.summary,
      relation_type_id: parseInt(this.newRelation.relation_type_id as unknown as string),
      priority_id: searchResult.priority_id,
      project_id: searchResult.project_id,
      project_slug: searchResult.project_slug,
    } as unknown as RelatedTicket

    this.relatedTickets.push({
      ...newRelation,
    })

    this.searchResults = []
    this.activeNewRelationId = null
    this.newRelation = {
      summary: '',
      relation_type_id: 1,
    } as NewRelation
  },
}))
