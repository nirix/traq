import Alpine from "alpinejs"
import axios from "axios"

Alpine.data("popoverConfirm", ({ position, message, success, remote, post }) => ({
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

      if (post) {
        const form = document.createElement("form")
        form.setAttribute("method", "post")
        form.setAttribute("action", post)
        form.id = "popover-confirm-post-form"
        document.body.append(form)
        form.submit()
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

    let right
    if (position === "center") {
      right = rootRect.right - rect.width / 2 - rootRect.width / 2
    } else {
      right = rootRect.right - rect.width
    }

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
