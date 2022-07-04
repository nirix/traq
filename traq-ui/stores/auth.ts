import { defineStore } from "pinia"

export interface User {
  id: number
  username: string
  name: string
  groupId: number
  isAdmin: boolean
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
    name(state) {
      return state.user?.name
    },
  },
  actions: {
    setAuth(user: User): void {
      this.user = user
    },
    can(action: string): boolean {
      if (this.user?.isAdmin) {
        return true
      }

      return this.user?.permissions ? this.user?.permissions[action] : false
    },
    canOneOf(permissions: string[]): boolean {
      return permissions.map((p) => this.can(p)).includes(true)
    },
  },
})
