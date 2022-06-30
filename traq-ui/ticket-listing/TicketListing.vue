<script lang="ts">
import axios from "axios"
import TicketFilters from "./TicketFilters.vue"

export default {
  components: { TicketFilters },
  data() {
    return {
      tickets: [],
      page: 1,
      total_pages: 1,
      sort_by: null,
      sort_order: "desc",
      filters: [],
    }
  },
  methods: {
    getTickets() {
      let ticketsUrl = window.traq.base + window.traq.project_slug + "/tickets.json"
      if (this.sort_by) {
        ticketsUrl = `${ticketsUrl}?order_by=${this.sort_by}.${this.sort_order}`
      }
      axios.get(ticketsUrl).then((resp) => {
        this.tickets = resp.data.tickets
        this.page = resp.data.page
        this.total_pages = resp.data.total_pages
      })
    },
    sortTickets(column) {
      this.sort_order = column !== this.sort_by || this.sort_order === "desc" ? "asc" : "desc"
      this.sort_by = column
      this.getTickets()
    },
    ticketUrl(ticketId) {
      return `${window.traq.base}${window.traq.project_slug}/tickets/${ticketId}`
    },
    userUrl(userId) {
      return `${window.traq.base}users/${userId}`
    },
    applyFilters(filters) {
      console.log(filters)
      this.filters = filters
    },
  },
  mounted() {
    this.getTickets()
  },
}
</script>

<template>
  <div class="content">
    <h2 id="page_title">Tickets</h2>
  </div>
  <TicketFilters @apply-filters="applyFilters" />
  <table id="tickets" class="ticket-listing list">
    <thead>
      <tr>
        <th>
          <a href="#" @click="sortTickets('ticket_id')">ID</a>
        </th>
        <th>
          <a href="#" @click="sortTickets('summary')">Summary</a>
        </th>
        <th>
          <a href="#" @click="sortTickets('status')">Status</a>
        </th>
        <th>
          <a href="#" @click="sortTickets('user')">Owner</a>
        </th>
        <th>Type</th>
        <th>Component</th>
        <th>Milestone</th>
      </tr>
      <tr v-for="ticket in tickets" :key="ticket.id" :class="'priority-' + ticket.priority.id">
        <td>{{ ticket.id }}</td>
        <td>
          <a :href="ticketUrl(ticket.id)">
            {{ ticket.summary }}
          </a>
        </td>
        <td>{{ ticket.status.name }}</td>
        <td>
          <a :href="userUrl(ticket.user.id)">{{ ticket.user.name }}</a>
        </td>
        <td>{{ ticket.type.name }}</td>
        <td>{{ ticket.component?.name ?? "-" }}</td>
        <td>{{ ticket.milestone?.name ?? "-" }}</td>
      </tr>
    </thead>
  </table>
</template>

<style scoped lang="postcss">
table.ticket-listing {
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
