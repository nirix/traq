import axios from "axios"
import "./css/main.css"
import "./main/FontAwesome"
import EasyMDE from "easymde"

window.EasyMDE = EasyMDE

// Alpinejs for simpler UI tasks
import Alpine from "alpinejs"

Alpine.directive("mde", (el, { value, modifiers, expression }, { Alpine, effect, cleanup, evaluate }) => {
  try {
    const options = evaluate(expression)

    const mde = new EasyMDE({
      element: el,
      autoDownloadFontAwesome: false,
      minHeight: options.height ?? "150px",
      status: false,
      uploadImage: false,
    })
  } catch {
    console.error("Unable to initialise EasyMDE")
  }
})

Alpine.data("popoverConfirm", ({ message, success, remote }) => ({
  open: false,
  loading: false,

  init() {
    const el = document.createElement("div")
    el.id = "popover-confirm"
    el.classList.add("popover-confirm")
    this.el = el

    const messageEl = document.createElement("div")
    messageEl.innerHTML = message ?? "Are you sure?"
    messageEl.classList.add("popover-confirm-content")

    const actionsEl = document.createElement("div")
    actionsEl.classList.add("popover-confirm-actions")

    const cancelBtn = document.createElement("button")
    cancelBtn.classList.add("btn")
    cancelBtn.textContent = window.language.no
    cancelBtn.addEventListener("click", () => {
      this.open = false
    })

    this.acceptBtn = document.createElement("button")
    this.acceptBtn.classList.add("btn-danger")
    this.acceptBtn.textContent = window.language.yes
    this.acceptBtn.addEventListener("click", () => {
      if (remote) {
        this.loading = true

        axios.post(remote).then((resp) => {
          this.open = false
          this.loading = false
          success(resp)
        })

        return
      }
      this.open = false

      if (success) {
        success()
      }
    })

    actionsEl.append(cancelBtn)
    actionsEl.append(this.acceptBtn)

    el.append(messageEl)
    el.append(actionsEl)

    this.$watch("open", () => {
      if (this.open) {
        this.openPopover()
        return
      }

      this.closePopover()
    })

    this.$watch("loading", () => {
      if (this.loading) {
        this.acceptBtn.innerHTML = '<span class="fas fa-fw fa-spin fa-circle-notch"></span>'
      } else {
        this.acceptBtn.textContent = window.language.yes
      }
    })
  },

  toggle() {
    this.open = !this.open
  },

  openPopover() {
    document.body.prepend(this.el)

    this.position()

    this.resizeEvent = window.addEventListener("resize", () => {
      this.position()
    })
  },

  position() {
    const rootRect = this.$root.getBoundingClientRect()
    const rect = this.el.getBoundingClientRect()
    const top = rootRect.y + rootRect.height + 5 + window.scrollY
    const right = rootRect.right - rect.width

    this.el.style.top = `${top}px`
    this.el.style.left = `${right}px`
  },

  close() {
    this.open = false
  },

  closePopover() {
    window.removeEventListener("resize", this.resizeEvent)
    this.el.remove()
  },
}))

window.Alpine = Alpine
Alpine.start()
