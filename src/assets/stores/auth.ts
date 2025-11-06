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
});
