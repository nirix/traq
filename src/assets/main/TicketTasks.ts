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
