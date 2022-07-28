import "./css/main.css"
import "./main/FontAwesome"
import EasyMDE from "easymde"

window.EasyMDE = EasyMDE

// Alpinejs for simpler UI tasks
import Alpine from "alpinejs"

Alpine.directive("mde", (el, { value, modifiers, expression }, { Alpine, effect, cleanup, evaluate }) => {
  try {
    const options = expression ? evaluate(expression) : {}

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

import "./main/PopoverConfirm"
import "./main/TicketTasks"
import "./main/RemoteMDE"

window.Alpine = Alpine
Alpine.start()
