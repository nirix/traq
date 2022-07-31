<script lang="ts">
import { onMounted } from "vue"
import axios from "axios"
import { DateTime } from "luxon"
import { faChevronDown, faChevronUp } from "@fortawesome/free-solid-svg-icons"
import TicketFilters from "./TicketFilters.vue"
import TicketColumns from "./TicketColumns.vue"
import MassActions from "./MassActions.vue"
import { useAuthStore } from "../stores/auth"
import PaginationLinks from "../components/PaginationLinks.vue"
import SkeletonLoader from "../components/SkeletonLoader.vue"
import { useProjectStore } from "../stores/project"

export default {
  components: { TicketFilters, TicketColumns, MassActions, PaginationLinks, SkeletonLoader },

  setup() {
    const auth = useAuthStore()
    const currentProject = useProjectStore()

    return {
      auth,
      currentProject,
    }
  },

  data() {
    return {
      isLoading: true,
      tickets: [],
      currentPage: 1,
      totalPages: 1,
      sortBy: null,
      sortOrder: "desc",
      filters: [],
      columns: ["ticket_id", "summary", "status", "type", "owner", "component", "milestone"],
      customFields: [],
      checkedTickets: [],
    }
  },

  mounted() {
    const customFieldsUrl = `${window.traq.base}api/${window.traq.project_slug}/custom-fields`

    Promise.all([axios.get(customFieldsUrl)]).then(([customFields]) => {
      this.customFields = customFields.data
      this.isLoading = false
    })
  },

  watch: {
    isLoading(newVal) {
      if (newVal === true) {
        this.getTickets()
      }
    },
  },

  computed: {
    getTicketUrl(): string {
      let ticketsUrl = window.traq.base + "api/" + window.traq.project_slug + "/tickets.json"

      // Add sorting options to URL
      if (this.sortBy) {
        ticketsUrl = `${ticketsUrl}?order_by=${this.sortBy}.${this.sortOrder}`
      }

      // Add filters to URL
      if (this.filters.length) {
        const filterBits = this.filters.map((filter) => {
          return `${filter.field}=` + (filter.condition ? "" : "!") + filter.values.join(",")
        })

        ticketsUrl = ticketsUrl + (this.sortBy ? "&" : "?") + filterBits.join("&")
      }

      if (this.currentPage > 1) {
        ticketsUrl = ticketsUrl + (ticketsUrl.includes("?") ? "&" : "?") + `page=${this.currentPage}`
      }

      return ticketsUrl
    },
    // Icons
    faChevronUp() {
      return faChevronUp
    },
    faChevronDown() {
      return faChevronDown
    },
  },

  methods: {
    getTickets(): void {
      axios.get(this.getTicketUrl).then((resp) => {
        this.tickets = resp.data.tickets
        this.currentPage = resp.data.page
        this.totalPages = resp.data.total_pages
      })
    },
    sortTickets(column): void {
      this.sortOrder = column !== this.sortBy || this.sortOrder === "desc" ? "asc" : "desc"
      this.sortBy = column
      this.getTickets()
      this.updateUrl()
    },
    ticketUrl(ticketId): string {
      return `${window.traq.base}${window.traq.project_slug}/tickets/${ticketId}`
    },
    userUrl(userId): string {
      return `${window.traq.base}users/${userId}`
    },
    applyFilters(filters): void {
      this.filters = filters
      this.getTickets()
      this.updateUrl()
    },
    updateColumns(columns: Array<string>): void {
      this.columns = columns
    },
    formatDate(date): string {
      return date ? DateTime.fromSQL(date, { zone: "UTC" }).toLocal().toRelative() : "-"
    },
    updateUrl() {
      // Update page URL to the same URL to fetch tickets without the .json extension.
      this.$router.push(this.getTicketUrl.replace(".json", "").replace("/api", ""))
    },
    toggleTicket(ticketId): void {
      if (this.checkedTickets.includes(ticketId)) {
        this.checkedTickets = this.checkedTickets.filter((checkedId) => checkedId !== ticketId)
      } else {
        this.checkedTickets.push(ticketId)
      }
    },
    massActionsTogglePage(event): void {
      const ticketIds: number[] = this.tickets.map((ticket) => ticket.ticket_id)

      if (event.target.checked) {
        // If checked, add (missing) ticket ids to checked list
        const missingIds = ticketIds.filter((ticketId) => !this.checkedTickets.includes(ticketId))
        this.checkedTickets = [...this.checkedTickets, ...missingIds]
      } else {
        // If unchecked, remove ticket ids
        this.checkedTickets = this.checkedTickets.filter((ticketId) => !ticketIds.includes(ticketId))
      }
    },
    massActionsUpdated(): void {
      this.getTickets()
      this.checkedTickets = []
    },
    changePage(page: number): void {
      this.currentPage = page
      this.$refs["massActionsAllToggler"].checked = false
      this.getTickets()
      this.updateUrl()
    },
  },
}
</script>

<template>
  <div class="content">
    <h2 id="page_title">Tickets</h2>
  </div>
  <template v-if="isLoading">
    <SkeletonLoader class="mb-5" />
    <SkeletonLoader class="mb-5" />
  </template>
  <template v-if="!isLoading">
    <TicketFilters @apply-filters="applyFilters" :custom-fields="customFields" v-if="!isLoading" />
    <TicketColumns @apply-columns="updateColumns" :custom-fields="customFields" v-if="!isLoading" />
  </template>
  <table class="ticket-listing">
    <thead>
      <tr>
        <th v-if="auth.can('perform_mass_actions')">
          <input type="checkbox" name="mass_actions_current_page" @click="massActionsTogglePage" ref="massActionsAllToggler" />
        </th>
        <th v-if="columns.includes('ticket_id')">
          <a href="#" @click="sortTickets('ticket_id')">ID</a>
          <template v-if="sortBy === 'ticket_id'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('summary')">
          <a href="#" @click="sortTickets('summary')">Summary</a>
          <template v-if="sortBy === 'summary'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('status')">
          <a href="#" @click="sortTickets('status')">Status</a>
          <template v-if="sortBy === 'status'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('owner')">
          <a href="#" @click="sortTickets('user')">Owner</a>
          <template v-if="sortBy === 'user'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('type')">
          <a href="#" @click="sortTickets('type')">Type</a>
          <template v-if="sortBy === 'type'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('component')">
          <a href="#" @click="sortTickets('component')">Component</a>
          <template v-if="sortBy === 'component'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('milestone')">
          <a href="#" @click="sortTickets('milestone')">Milestone</a>
          <template v-if="sortBy === 'milestone'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('assigned_to')">
          <a href="#" @click="sortTickets('assigned_to')">Assignee</a>
          <template v-if="sortBy === 'assigned_to'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('priority')">
          <a href="#" @click="sortTickets('priority')">Priority</a>
          <template v-if="sortBy === 'priority'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('severity')">
          <a href="#" @click="sortTickets('severity')">Severity</a>
          <template v-if="sortBy === 'severity'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('created_at')">
          <a href="#" @click="sortTickets('created_at')">Reported</a>
          <template v-if="sortBy === 'created_at'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('updated_at')">
          <a href="#" @click="sortTickets('updated_at')">Updated</a>
          <template v-if="sortBy === 'updated_at'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <th v-if="columns.includes('votes')">
          <a href="#" @click="sortTickets('votes')">Votes</a>
          <template v-if="sortBy === 'votes'">
            <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
            <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
          </template>
        </th>
        <template v-for="field in customFields" :key="field.slug">
          <th v-if="columns.includes(field.slug)">
            <a href="#" @click="sortTickets(field.slug)">{{ field.name }}</a>
            <template v-if="sortBy === field.slug">
              <fa-icon :icon="faChevronUp" v-if="sortOrder === 'asc'" />
              <fa-icon :icon="faChevronDown" v-if="sortOrder === 'desc'" />
            </template>
          </th>
        </template>
      </tr>
      <TransitionGroup enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <tr v-for="ticket in tickets" :key="ticket.id" :class="'priority-' + ticket.priority.id">
          <td v-if="auth.can('perform_mass_actions')">
            <input
              type="checkbox"
              name="mass_actions[]"
              :value="ticket.ticket_id"
              :checked="checkedTickets.includes(ticket.ticket_id)"
              @click="toggleTicket(ticket.ticket_id)"
            />
          </td>
          <td v-if="columns.includes('ticket_id')">{{ ticket.ticket_id }}</td>
          <td v-if="columns.includes('summary')">
            <a :href="ticketUrl(ticket.ticket_id)">{{ ticket.summary }}</a>
          </td>
          <td v-if="columns.includes('status')">{{ ticket.status.name }}</td>
          <td v-if="columns.includes('owner')">
            <a :href="userUrl(ticket.user.id)">{{ ticket.user.name }}</a>
          </td>
          <td v-if="columns.includes('type')">{{ ticket.type.name }}</td>
          <td v-if="columns.includes('component')">{{ ticket.component?.name ?? "-" }}</td>
          <td v-if="columns.includes('milestone')">{{ ticket.milestone?.name ?? "-" }}</td>
          <td v-if="columns.includes('assigned_to')">
            <a v-if="ticket.assigned_to" :href="userUrl(ticket.assigned_to.id)">{{ ticket.assigned_to.name }}</a>
            <template v-if="!ticket.assigned_to">-</template>
          </td>
          <td v-if="columns.includes('priority')">{{ ticket.priority.name }}</td>
          <td v-if="columns.includes('severity')">{{ ticket.severity.name }}</td>
          <td v-if="columns.includes('created_at')">{{ formatDate(ticket.created_at) }}</td>
          <td v-if="columns.includes('updated_at')">{{ formatDate(ticket.updated_at) }}</td>
          <td v-if="columns.includes('votes')">{{ ticket.votes }}</td>
          <template v-for="field in customFields" :key="field.slug">
            <td v-if="columns.includes(field.slug)">{{ ticket.custom_fields[field.slug.replaceAll("-", "_")] ?? "-" }}</td>
          </template>
        </tr>
      </TransitionGroup>
    </thead>
  </table>
  <PaginationLinks :current-page="currentPage" :total-pages="totalPages" @navigate="changePage" />

  <MassActions :ticket-ids="checkedTickets" v-if="auth.can('perform_mass_actions')" @on-update="massActionsUpdated" />
</template>

<style scoped lang="postcss">
table.ticket-listing {
  & th {
    & > svg {
      float: right;
    }
  }

  & tr {
    &.priority-1 {
      & td {
        @apply bg-red-50;
      }

      &:nth-child(even) td {
        @apply bg-red-100;
      }
    }

    &.priority-2 {
      & td {
        @apply bg-yellow-50;
      }

      &:nth-child(even) td {
        @apply bg-yellow-100;
      }
    }

    &.priority-3 {
      & td {
        @apply bg-sky-50;
      }

      &:nth-child(even) td {
        @apply bg-sky-100;
      }
    }

    &.priority-4 {
      & td {
        @apply bg-violet-50;
      }

      &:nth-child(even) td {
        @apply bg-violet-100;
      }
    }

    &.priority-5 {
      & td {
        @apply bg-gray-50;
      }

      &:nth-child(even) td {
        @apply bg-gray-100;
      }
    }

    &:hover td,
    &:hover:nth-child(even) td {
      @apply bg-white;
    }
  }
}
</style>