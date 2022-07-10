import { createApp } from "vue"
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { createPinia } from "pinia"
import * as VueRouter from "vue-router"
import App from "./App.vue"
import TicketListing from "./ticket-listing/TicketListing.vue"
import ViewTicket from "./ticket/ViewTicket.vue"

const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes: [
    { name: "tickets", path: "/:project/tickets", component: TicketListing },
    { name: "ticket", path: "/:project/tickets/:ticket", component: ViewTicket },
  ],
})

const pinia = createPinia()

const app = createApp(App)
app.use(router)
app.use(pinia)
app.component("fa-icon", FontAwesomeIcon)

app.mount("#vue-tickets")
