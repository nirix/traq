import { createApp } from "vue"
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"

import TicketListing from "./TicketListing.vue"

const app = createApp(TicketListing).component("fa-icon", FontAwesomeIcon)
app.mount("#ticket-listing")
