/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

export interface User {
  id: number
  username: string
  name: string
  group_id: number
  is_admin: boolean
  permissions: { [key: string]: boolean }
  group: {
    is_admin: boolean
  }
}

export interface IAuthStore {
  user: User | null
  init: () => void
  getUser: (project: string) => void
  can: (action: string) => boolean
  canOneOf: (permissions: string[]) => boolean
  isAdmin: () => boolean
}

Alpine.store('auth', {
  user: null as User | null,

  init() {
    this.getUser(window.traq.project_slug)
  },

  getUser(project: string) {
    let authUrl = `${window.traq.base}api/auth`

    if (project) {
      authUrl = `${authUrl}/${project}`
    }

    axios.get(authUrl).then((resp) => {
      this.user = resp.data
    })
  },

  can(action: string) {
    return this.user?.permissions && this.user?.permissions[action] === true
  },

  canOneOf(permissions: string[]) {
    return permissions.map((p) => this.can(p)).includes(true)
  },

  isAdmin() {
    return this.user?.group?.is_admin === true
  },
} as IAuthStore)
