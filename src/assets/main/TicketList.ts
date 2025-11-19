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
import type { CustomFieldInterface, FilterInterface, TicketInterface } from './../interfaces'

interface FilterOption {
  label?: string
  field: string
  type: "is" | "isOr" | "contains"
  condition?: boolean
  dataSet?: string
  value?: Array<string | number>
  count?: number
}

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

  isLoading: false,
  showColumnSettings: true,
  showFilters: true,
  enableMassActions: false,

  page: 1,
  totalPages: 1,
  sortColumn: null as string | null,
  sortOrder: 'asc',

  tickets: [],
  filters: [] as FilterInterface[],
  filterData: {
    milestones: [] as Array<{ label: string; value: string }>,
    statuses: {} as { [group: string]: Array<{ label: string; value: string }>, },
    priorities: [] as Array<{ label: string; value: string }>,
    components: [] as Array<{ label: string; value: string }>,
    types: [] as Array<{ label: string; value: string }>,
    assignees: [] as Array<{ label: string; value: string }>,
  } as Record<string, Array<{ label: string; value: string }> | { [group: string]: Array<{ label: string; value: string }> }>,
  customFields: [] as CustomFieldInterface[],
  checkedTickets: [] as number[],
  massActions: {
    comment: '',
    status: '-1',
    type: '-1',
    priority: '-1',
    milestone: '-1',
    component: '-1',
    assignee: '-1',
  },

  availableFilters: [
    {
      field: "summary",
      type: "contains",
    },
    {
      field: "description",
      type: "contains",
    },
    {
      field: "type",
      type: "is",
      dataSet: "types",
    },
    {
      field: "owner",
      type: "isOr",
    },
    {
      field: "assigned_to",
      type: "is",
      dataSet: "assignees",
    },
    {
      field: "component",
      type: "is",
      dataSet: "components",
    },
    {
      field: "milestone",
      type: "is",
      dataSet: "milestones",
    },
    {
      field: "version",
      type: "is",
      dataSet: "milestones",
    },
    {
      field: "status",
      type: "is",
      dataSet: "statuses",
    },
    {
      field: "priority",
      type: "is",
      dataSet: "priorities",
    },
  ] as FilterOption[],

  init() {
    this.enableMassActions = this.$el.hasAttribute('data-mass-actions')
    this.page = parseInt((new URLSearchParams(window.location.search)).get('page') || '1', 10) || 1

    const roadmapUrl = window.traq.base + window.traq.project_slug + "/roadmap/all.json"
    const componentsUrl = window.traq.base + "api/" + window.traq.project_slug + "/components"
    const membersUrl = window.traq.base + "api/" + window.traq.project_slug + "/members"
    const statusesUrl = window.traq.base + "api/statuses"
    const prioritiesUrl = window.traq.base + "api/priorities"
    const typesUrl = window.traq.base + "api/types"
    const customFieldsUrl = window.traq.base + 'api/' + window.traq.project_slug + '/custom-fields'

    Promise.all([
      axios.get(roadmapUrl),
      axios.get(statusesUrl),
      axios.get(prioritiesUrl),
      axios.get(componentsUrl),
      axios.get(typesUrl),
      axios.get(membersUrl),
      axios.get(customFieldsUrl),
    ]).then(([roadmap, statuses, priorities, components, ticketTypes, members, customFields]) => {
      this.filterData.milestones =
        roadmap.data.map((data: Record<string, unknown>) => ({
          label: data.name,
          value: data.slug,
          id: data.id,
        })) ?? []

      const open =
        statuses.data
          .filter((status: Record<string, unknown>) => status.status === 1)
          .map((data: Record<string, unknown>) => ({
            label: data.name,
            value: data.name,
            id: data.id,
          })) ?? []

      const closed =
        statuses.data
          .filter((status: Record<string, unknown>) => status.status === 0)
          .map((data: Record<string, unknown>) => ({
            label: data.name,
            value: data.name,
            id: data.id,
          })) ?? []

      const started =
        statuses.data
          .filter((status: Record<string, unknown>) => status.status === 2)
          .map((data: Record<string, unknown>) => ({
            label: data.name,
            value: data.name,
            id: data.id,
          })) ?? []

      this.filterData.statuses = {
        Open: open,
        Started: started,
        Closed: closed,
      }

      this.filterData.priorities =
        priorities.data.map((data: Record<string, unknown>) => ({
          label: data.name,
          value: data.name,
          id: data.id,
        })) ?? []

      this.filterData.components =
        components.data.map((data: Record<string, unknown>) => ({
          label: data.name,
          value: data.name,
          id: data.id,
        })) ?? []

      this.filterData.types =
        ticketTypes.data.map((data: Record<string, unknown>) => ({
          label: data.name,
          value: data.name,
          id: data.id,
        })) ?? []

      this.filterData.assignees =
        members.data.map((data: Record<string, unknown>) => ({
          label: data.name,
          value: data.username,
          id: data.id,
        })) ?? []

      this.customFields = customFields.data

      // Convert query string after we get statuses, as we convert 'allOpen' and 'allClosed'
      this.buildCustomFields().then(() => this.convertQueryString())
    })

    this.$watch('page', () => {
      this.updateUrl()
      this.fetchTickets()
    })
  },

  convertQueryString() {
    const filters: FilterInterface[] = []
    const params = new URLSearchParams(window.location.search)

    params.forEach((value, key) => {
      const filterOption = this.availableFilters.find(f => f.field === key)
      if (!filterOption) {
        return
      }

      let condition = true
      let values: string[] = []

      if (value.startsWith('!')) {
        condition = false
        value = value.substring(1)
      }

      // Handle allopen, allstarted and allclosed status filter values
      if (filterOption.field === 'status' && value === 'allopen') {
        // @ts-expect-error tsfu
        // merge Open and Started statuses
        values = [...this.filterData.statuses.Open.map((status: { label: string; value: string }) => status.value), ...this.filterData.statuses.Started.map((status: { label: string; value: string }) => status.value)]
      } else if (filterOption.field === 'status' && value === 'allclosed') {
        // @ts-expect-error tsfu
        values = this.filterData.statuses.Closed.map((status: { label: string; value: string }) => status.value)
      } else if (filterOption.field === 'status' && value === 'allstarted') {
        // @ts-expect-error tsfu
        values = this.filterData.statuses.Started.map((status: { label: string; value: string }) => status.value)
      } else {
        values = value.split(',')
      }

      filters.push({
        ...filterOption,
        condition,
        values,
      })
    })

    this.filters = filters

    this.fetchTickets()
  },

  fetchUrl() {
    let url = `${window.traq.base}${window.traq.project_slug}/tickets.json`

    const params = new URLSearchParams()

    // Apply filters
    if (this.filters.length) {
      this.filters.map((filter: FilterInterface) => {
        params.set(filter.field, (filter.condition ? "" : "!") + filter.values.join(","))
      })
    }

    if (this.page > 1) {
      params.set('page', this.page.toString())
    } else {
      params.delete('page')
    }

    if (this.sortColumn) {
      params.set('order_by', `${this.sortColumn}.${this.sortOrder}`)
    }

    const paramString = params.toString()
    if (paramString) {
      url += `?${paramString}`
    }

    return url
  },

  updateUrl() {
    window.history.replaceState({}, '', this.fetchUrl().replace('.json', ''))
  },

  fetchTickets() {
    this.isLoading = true
    axios.get(this.fetchUrl())
      .then(response => {
        this.tickets = response.data.tickets
        this.page = response.data.page
        this.totalPages = response.data.total_pages
        this.isLoading = false
        this.checkedTickets = []
      })
      .catch(err => {
        console.error(err)
        this.isLoading = false
      })
  },

  sort(column: string) {
    if (this.sortColumn === column) {
      this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
    }

    this.sortColumn = column

    this.fetchTickets()
  },

  addFilter(field: string) {
    const filter = this.availableFilters.find(f => f.field === field);
    (this.$refs.newFilterSelect as HTMLSelectElement).value = ''

    if (!filter) {
      return
    }

    this.filters.push({
      ...filter,
      condition: true,
      values: ["isOr", "contains"].includes(filter.type) ? [""] : [],
    })
  },
  addFilterValue(filter: FilterInterface): void {
      filter.values.push("")
    },
  removeFilter(filter: FilterInterface, valueIndex: number | null = null) {
    if (valueIndex === null || (valueIndex === 0 && filter.values.length === 1)) {
      this.filters = this.filters.filter((option: FilterInterface) => option.field !== filter.field)
    } else {
      filter.values.splice(valueIndex, 1)
    }
  },
  setFilterValue(filter: FilterInterface, index: number, event: Event): void {
    filter.values[index] = (event.target as HTMLInputElement).value
  },
  clearFilters() {
    this.filters = []
    this.updateUrl()
    this.fetchTickets()
  },
  applyFilters() {
    this.updateUrl()
    this.fetchTickets()
  },

  prevPageUrl() {
    if (this.page <= 1) {
      return '#'
    }

    return `${window.traq.base}${window.traq.project_slug}/tickets?page=${this.page - 1}`
  },
  nextPageUrl() {
    if (this.page >= this.totalPages) {
      return '#'
    }

    return `${window.traq.base}${window.traq.project_slug}/tickets?page=${this.page + 1}`
  },
  prevPage() {
    if (this.page <= 1) {
      return
    }

    this.page--
    document.getElementById('ticket-listing')?.scrollIntoView({ behavior: 'smooth' })
  },
  nextPage() {
    if (this.page >= this.totalPages) {
      return
    }

    this.page++
    document.getElementById('ticket-listing')?.scrollIntoView({ behavior: 'smooth' })
  },

  buildCustomFields(): Promise<boolean> {
    return new Promise((resolve, reject) => {
      try {
        // Map custom fields to available filters.
        for (const field of this.customFields) {
          const key: string = field.slug.replaceAll("-", "_")

          // If the values are an array, create a data set.
          if (Array.isArray(field.values)) {
            this.filterData[key] = field.values.map((value) => ({ label: value, value }))
          }

          // is/isOr will do just fine
          const fieldType: "is" | "isOr" = field.type === "select" ? "is" : "isOr"

          const filter: FilterOption = {
            label: field.name,
            field: field.slug,
            type: fieldType,
            dataSet: fieldType === "is" ? key : undefined,
          }

          this.availableFilters.push(filter)
        }

        resolve(true)
      } catch (error) {
        console.error(error)
        reject(false)
      }
    })
  },

  massActionsTogglePage() {
    if (this.checkedTickets.length === this.tickets.length) {
      this.checkedTickets = []
    } else {
      this.checkedTickets = this.tickets.map((ticket: TicketInterface) => ticket.ticket_id)
    }
  },
  toggleTicket(ticketId: number) {
    if (this.checkedTickets.includes(ticketId)) {
      this.checkedTickets = this.checkedTickets.filter((id) => id !== ticketId)
    }else {
      this.checkedTickets.push(ticketId)
    }
  },

  updateTickets() {
    const massActionsUrl = `${window.traq.base}${window.traq.project_slug}/tickets/mass-actions.json`

    // Build form data
    const formData = new FormData()
    this.checkedTickets.map((ticketId) => {
      formData.append("tickets[]", ticketId.toString())
    })
    formData.append("comment", this.massActions.comment)
    formData.append("status", this.massActions.status)
    formData.append("type", this.massActions.type)
    formData.append("priority", this.massActions.priority)
    formData.append("milestone", this.massActions.milestone)
    formData.append("component", this.massActions.component)
    formData.append("assigned_to", this.massActions.assignee)

    // Update tickets
    axios
      .post(massActionsUrl, formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      .then((resp) => {
        if (resp.data.success) {
          this.fetchTickets()
          this.checkedTickets = []
          this.massActions = {
            comment: '',
            status: '-1',
            type: '-1',
            priority: '-1',
            milestone: '-1',
            component: '-1',
            assignee: '-1',
          }
        }
      })
  },
}))
