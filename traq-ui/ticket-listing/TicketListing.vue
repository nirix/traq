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
    }
  },
  methods: {
    getTickets() {
      let ticketsUrl =
        window.traq.base + window.traq.project_slug + "/tickets.json"
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
      this.sort_order =
        column !== this.sort_by || this.sort_order === "desc" ? "asc" : "desc"
      this.sort_by = column
      this.getTickets()
    },
    ticketUrl(ticketId) {
      return `${window.traq.base}${window.traq.project_slug}/tickets/${ticketId}`
    },
    userUrl(userId) {
      return `${window.traq.base}users/${userId}`
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
    <TicketFilters />
  </div>
  <table id="tickets" class="ticket_listing list">
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
      <tr
        v-for="ticket in tickets"
        :key="ticket.id"
        :class="'priority_' + ticket.priority.id"
      >
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

<style></style>
