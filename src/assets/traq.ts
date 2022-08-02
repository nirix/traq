/*!
 * Traq
 * Copyright (C) 2009-2022 Jack Polgar
 * Copyright (C) 2012-2022 Traq.io
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

import { createApp } from "vue"
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { createPinia } from "pinia"
import * as VueRouter from "vue-router"
import App from "./App.vue"
import TicketListing from "./ticket-listing/TicketListing.vue"

const router = VueRouter.createRouter({
  history: VueRouter.createWebHistory(),
  routes: [{ name: "tickets", path: "/:project/tickets", component: TicketListing }],
})

const pinia = createPinia()

const app = createApp(App)
app.use(router)
app.use(pinia)
app.component("fa-icon", FontAwesomeIcon)

app.mount("#vue-traq")
