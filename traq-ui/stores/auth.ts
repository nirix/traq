import { useRoute } from "vue-router"
import axios from "axios"
import { defineStore } from "pinia"

export interface User {
  id: number
  username: string
  name: string
  group_id: number
  is_admin: boolean
  permissions: { [key: string]: boolean }
}

interface AuthStore {
  user: User
}

export const useAuthStore = defineStore("auth", {
  state: (): AuthStore => ({
    user: null,
  }),
  getters: {
    name(state): string {
      return state.user?.name
    },
    isAuthenticated(state): boolean {
      // Check if guest group id
      return state.user.group_id !== 3
    },
  },
  actions: {
    async getUser(project) {
      let authUrl = `${window.traq.base}api/auth`

      if (project) {
        authUrl = `${authUrl}/${project}`
      }

      axios.get(authUrl).then((resp) => {
        this.setAuth(resp.data)
      })
    },
    setAuth(user: User): void {
      this.user = user
    },
    can(action: string): boolean {
      if (this.user?.group?.is_admin) {
        return true
      }

      return this.user?.permissions && this.user?.permissions[action] === true
    },
    canOneOf(permissions: string[]): boolean {
      return permissions.map((p) => this.can(p)).includes(true)
    },
  },
})
