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

import axios from "axios"
import Alpine from "alpinejs"

Alpine.data("ticketTasks", ({ url, can }) => ({
  saving: {},

  toggle(id) {
    if (!can) {
      return
    }

    this.saving[id] = true

    axios
      .post(url + "/" + id)
      .then(() => {
        this.$data.complete = !this.$data.complete
      })
      .finally(() => {
        this.saving[id] = false
      })
  },

  isSaving(id) {
    return this.saving[id] ?? false
  },
}))
