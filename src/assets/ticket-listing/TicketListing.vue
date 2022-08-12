<script setup lang="ts">
import { onMounted, computed, ref, watch } from "vue"
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
import { useRouter } from "vue-router"
import type { CustomFieldInterface, FilterInterface, TicketInterface } from "../interfaces"

const router = useRouter()
const auth = useAuthStore()
const currentProject = useProjectStore()

const massActionsAllToggler = ref(null)
const isLoading = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const sortBy = ref<string | null>(null)
const sortOrder = ref<"asc" | "desc">("desc")
const filters = ref<FilterInterface[]>([])
const tickets = ref<TicketInterface[]>([])
const columns = ref<string[]>(["ticket_id", "summary", "status", "type", "owner", "component", "milestone"])
const customFields = ref<CustomFieldInterface[]>([])
const checkedTickets = ref<number[]>([])

watch(
  () => isLoading,
  (loading) => {
    if (loading) {
      getTickets()
    }
  }
)

const getTicketsUrl = computed(() => {
  let ticketsUrl = window.traq.base + "api/" + window.traq.project_slug + "/tickets.json"

  // Add sorting options to URL
  if (sortBy.value) {
    ticketsUrl = `${ticketsUrl}?order_by=${sortBy.value}.${sortOrder.value}`
  }

  // Add filters to URL
  if (filters.value.length) {
    const filterBits = filters.value.map((filter: FilterInterface) => {
      return `${filter.field}=` + (filter.condition ? "" : "!") + filter.values.join(",")
    })

    ticketsUrl = ticketsUrl + (sortBy.value ? "&" : "?") + filterBits.join("&")
  }

  if (currentPage.value > 1) {
    ticketsUrl = ticketsUrl + (ticketsUrl.includes("?") ? "&" : "?") + `page=${currentPage.value}`
  }

  return ticketsUrl
})

const getTickets = () => {
  axios.get(getTicketsUrl.value).then((resp) => {
    tickets.value = resp.data.tickets
    currentPage.value = resp.data.page
    totalPages.value = resp.data.total_pages
  })
}

const sortTickets = (column: string) => {
  sortOrder.value = column !== sortBy.value || sortOrder.value === "desc" ? "asc" : "desc"
  sortBy.value = column
  getTickets()
  updateUrl()
}

const ticketUrl = (ticketId: number): string => {
  return `${window.traq.base}${window.traq.project_slug}/tickets/${ticketId}`
}

const userUrl = (userId: number): string => {
  return `${window.traq.base}users/${userId}`
}

const applyFilters = (newFilters: FilterInterface[]): void => {
  filters.value = newFilters
  getTickets()
  updateUrl()
}

const updateColumns = (newColumns: string[]): void => {
  columns.value = newColumns
}

const formatDate = (date: DateTime): string => {
  return date ? DateTime.fromSQL(date, { zone: "UTC" }).toLocal().toRelative() : "-"
}

const updateUrl = () => {
  // Update page URL to the same URL to fetch tickets without the .json extension.
  router.push(getTicketsUrl.value.replace(".json", "").replace("/api", ""))
}

const toggleTicket = (ticketId: number): void => {
  if (checkedTickets.value.includes(ticketId)) {
    checkedTickets.value = checkedTickets.value.filter((checkedId) => checkedId !== ticketId)
  } else {
    checkedTickets.value.push(ticketId)
  }
}

const massActionsTogglePage = (event): void => {
  const ticketIds: number[] = tickets.value.map((ticket) => ticket.ticket_id)

  if (event.target.checked) {
    // If checked, add (missing) ticket ids to checked list
    const missingIds = ticketIds.filter((ticketId) => !checkedTickets.value.includes(ticketId))
    checkedTickets.value = [...checkedTickets.value, ...missingIds]
  } else {
    // If unchecked, remove ticket ids
    checkedTickets.value = checkedTickets.value.filter((ticketId: number) => !ticketIds.includes(ticketId))
  }
}

const massActionsUpdated = (): void => {
  getTickets()
  checkedTickets.value = []
}

const changePage = (page: number): void => {
  currentPage.value = page

  if (massActionsAllToggler.value) {
    massActionsAllToggler.value.checked = false
  }

  getTickets()
  updateUrl()
}

onMounted(() => {
  const customFieldsUrl = `${window.traq.base}api/${window.traq.project_slug}/custom-fields`

  Promise.all([axios.get(customFieldsUrl)]).then(([fieldsResp]) => {
    customFields.value = fieldsResp.data
    isLoading.value = false
  })
})
</script>

<template>
  <div class="content">
    <h2 class="page-title">Tickets</h2>
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
