import axios from "axios"
import Alpine from "alpinejs"

Alpine.data("navbarSearch", () => ({
  open: false,
  hasResults: false,
  results: {},

  fetchResults() {
    const data = {
      query: this.$refs.input.value,
      project: window.traq.project_slug,
    }

    axios.post(`${window.traq.base}api/search`, data).then((resp) => {
      this.results = resp.data ?? {}
      this.hasResults = this.results.milestones?.length > 0 || this.results.tickets?.length > 0
    })
  },

  ticketUrl(ticket) {
    return `${window.traq.base}${ticket.project.slug}/tickets/${ticket.ticket_id}`
  },

  milestoneUrl(milestone) {
    return `${window.traq.base}${milestone.project.slug}/milestone/${milestone.slug}`
  },
}))
