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

import "./css/main.css"
import "./main/FontAwesome"
import EasyMDE from "easymde"

window.EasyMDE = EasyMDE

// Alpinejs for simpler UI tasks
import Alpine from "alpinejs"

import "./main/PopoverConfirm"
import "./main/TicketTasks"
import "./main/TicketTemplates"
import "./main/RemoteMDE"
import "./main/EasyMDE"
import "./main/NavbarSearch"
import "./main/TicketList"
import "./main/RelatedTickets"
import "./stores/auth"

window.Alpine = Alpine
Alpine.start()
