import { createApp } from "vue"
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { createPinia } from "pinia"

import TicketListing from "./TicketListing.vue"

const pinia = createPinia()
const app = createApp(TicketListing)

app.component("fa-icon", FontAwesomeIcon)
app.use(pinia)

app.mount("#ticket-listing")
