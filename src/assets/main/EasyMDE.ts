import Alpine from "alpinejs"

Alpine.directive("mde", (el, { value, modifiers, expression }, { Alpine, effect, cleanup, evaluate }) => {
  try {
    const options = expression ? evaluate(expression) : {}

    // Don't initialise unless we need to.
    if (el.dataset["mded"] === "yes") {
      return
    }
    el.dataset["mded"] = "yes"

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
