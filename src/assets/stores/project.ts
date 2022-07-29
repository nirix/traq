import axios from "axios"
import { defineStore } from "pinia"

export interface Project {
  name: string
  slug: string
}

interface ProjectStore {
  project: Project
}

export const useProjectStore = defineStore("project", {
  state: (): ProjectStore => ({
    project: null,
  }),
  getters: {
    name(state): string {
      return state.project?.name
    },
  },
  actions: {
    async getProject(project) {
      const projectUrl = `${window.traq.base}${project}.json`

      axios.get(projectUrl).then((resp) => {
        this.project = resp.data
      })
    },
  },
})
