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

// Alpine.bind("PopoverConfirm", () => ({
//   "x-init"() {
//     console.log("test")
//   },
//   "@click"() {
//     console.log("tester")
//     alert("a")
//   },
//   ":disabled"() {
//     return true
//   },
// }))

Alpine.data("popoverConfirm", ({ message, success }) => ({
  open: false,

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

    const acceptBtn = document.createElement("button")
    acceptBtn.classList.add("btn-danger")
    acceptBtn.textContent = window.language.yes
    acceptBtn.addEventListener("click", () => {
      if (success) {
        this.open = false
        success()
      }
    })

    actionsEl.append(cancelBtn)
    actionsEl.append(acceptBtn)

    el.append(messageEl)
    el.append(actionsEl)

    this.$watch("open", () => {
      if (this.open) {
        this.openPopover()
        return
      }

      this.closePopover()
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
