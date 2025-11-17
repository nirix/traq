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
  priority_id: number
  project_slug: string
  related_ticket_id: number
  related_summary: string
  related_priority_id: number
  related_project_slug: string
  direct: boolean
  relation_type_id: number
  relation_type_name: string
  display_relation_type_id: number
}

interface RelationType {
  id: number
  name: string
  inverse_type_id: number
}

interface NewRelation {
  id: number
  ticket_id: number | null
  project_id: number | null
  relation_type_id: number
  summary: string
}

Alpine.data('relatedTickets', ({ ticketId, canEdit }: { ticketId: number, canEdit: boolean }) => ({
  url: '',
  ticketId,
  relatedTickets: [] as RelatedTicket[],
  relationTypes: [] as RelationType[],
  originalRelations: [] as RelatedTicket[],
  newRelations: [] as NewRelation[],
  changed: false,
  open: true,
  canEdit,
  searchResults: [],
  activeNewRelationId: null as number | null,

  init() {
    this.url = window.traq.base + window.traq.project_slug + "/tickets/" + this.ticketId + "/related-tickets"

    axios.get(this.url).then((resp) => {
      this.relationTypes = resp.data.relation_types
      this.relatedTickets = resp.data.related_tickets.map((relatedTicket: RelatedTicket) => this.parseRelation(relatedTicket))
      this.originalRelations = resp.data.related_tickets.map((relatedTicket: RelatedTicket) => this.parseRelation(relatedTicket))
    })

    // compare originalRelations with relatedTickets and if they are different, update the relatedTickets
    this.$watch('relatedTickets', (relatedTickets) => {
      this.changed = this.originalRelations.some((originalRelation, index) => {
        return originalRelation.relation_type_id !== relatedTickets[index].relation_type_id
      })
    })
  },

  parseRelation(relatedTicket: RelatedTicket) {
    const inverseTypeId = this.relationTypes.find((relationType: RelationType) => relationType.id === relatedTicket.relation_type_id)?.inverse_type_id

    return {
      ...relatedTicket,
      display_relation_type_id: relatedTicket.direct ? relatedTicket.relation_type_id : inverseTypeId,
    }
  },

  ticketHref(relatedTicket: RelatedTicket) {
    if (relatedTicket.direct) {
      return window.traq.base + relatedTicket.project_slug + "/tickets/" + relatedTicket.related_ticket_id
    }

    return window.traq.base + relatedTicket.related_project_slug + "/tickets/" + relatedTicket.ticket_id
  },

  relationPriority(relatedTicket: RelatedTicket) {
    return relatedTicket.direct ? relatedTicket.related_priority_id : relatedTicket.priority_id
  },

  relationTicketId(relatedTicket: RelatedTicket) {
    return relatedTicket.direct ? relatedTicket.related_ticket_id : relatedTicket.ticket_id
  },

  relationSummary(relatedTicket: RelatedTicket) {
    return relatedTicket.direct ? relatedTicket.related_summary : relatedTicket.summary
  },

  updateRelationType(relatedTicket: RelatedTicket, relationTypeId: number) {
    const newRelationTypeId = parseInt(relationTypeId as unknown as string)
    const inverseTypeId = this.relationTypes.find((relationType: RelationType) => relationType.id === newRelationTypeId)?.inverse_type_id ?? 1

    if (relatedTicket.direct) {
      console.log('direct')
      relatedTicket.relation_type_id = newRelationTypeId
      relatedTicket.display_relation_type_id = newRelationTypeId
    } else {
      console.log('inverse')
      relatedTicket.relation_type_id = inverseTypeId
      relatedTicket.display_relation_type_id = newRelationTypeId
    }
  },

  remove(relatedTicket: RelatedTicket) {
    this.relatedTickets = this.relatedTickets.filter((relation) => relation.id !== relatedTicket.id)
  },

  removeNewRelation(newRelation: NewRelation) {
    this.newRelations = this.newRelations.filter((relation) => relation.id !== newRelation.id)
  },

  save() {

    const data = this.relatedTickets.map((relatedTicket) => ({
      ...relatedTicket,
    })).concat(this.newRelations.map((newRelation) => ({
      ...newRelation,
    } as unknown as RelatedTicket)))

    // axios.post(this.url, {
    //   related_tickets: data,
    // }).then((resp) => {
    //   this.relatedTickets = resp.data.related_tickets.map((relatedTicket: RelatedTicket) => this.parseRelation(relatedTicket))
    //   this.originalRelations = resp.data.related_tickets.map((relatedTicket: RelatedTicket) => this.parseRelation(relatedTicket))
    //   this.changed = false
    // })

    console.log(data)
  },

  cancel() {
    this.newRelations = []
    this.relatedTickets = this.originalRelations.map((originalRelation) => {
      return {
        ...originalRelation,
      }
    })
  },

  add() {
    this.newRelations.push({
      id: this.newRelations.length + 1,
      ticket_id: null,
      project_id: null,
      relation_type_id: 1,
      summary: '',
    })
  },

  focusSummary(newRelation: NewRelation) {
    this.activeNewRelationId = newRelation.id
  },

  blurSummary(target: HTMLElement) {
    // Check if we clicked into the search results
    if (target.closest('.related-tickets-search-results')) {
      return
    }

    // this.focusedSummary = null
    this.searchResults = []
  },

  searchTicket(newRelation: NewRelation, element: HTMLInputElement) {
    console.log('search for ticket')

    axios.get(window.traq.base + window.traq.project_slug + "/tickets.json?summary=" + newRelation.summary).then((resp) => {
      this.searchResults = resp.data.tickets

      // set x/y to position of focusedSummary, taking into account scroll position
      this.$refs.searchResults.style.top = element.getBoundingClientRect().bottom + window.scrollY + 'px'
      this.$refs.searchResults.style.left = element.getBoundingClientRect().left + window.scrollX + 'px'
    })
  },

  selectTicket(searchResult: TicketInterface) {
    const newRelation = this.newRelations.find((relation) => relation.id === this.activeNewRelationId)
    if (!newRelation) {
      return
    }

    newRelation.ticket_id = searchResult.ticket_id
    newRelation.project_id = searchResult.project_id
    newRelation.summary = searchResult.summary

    this.searchResults = []
    this.activeNewRelationId = null
  },
}))
