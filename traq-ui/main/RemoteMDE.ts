import Alpine from "alpinejs"
import axios from "axios"
import DOMPurify from "dompurify"
import { marked } from "marked"

Alpine.data("remoteMde", ({ url, height }) => ({
  saving: false,
  original: "",

  init() {
    this.original = this.$refs.editor.value

    this.mde = new EasyMDE({
      element: this.$refs.editor,
      autoDownloadFontAwesome: false,
      minHeight: height ?? "150px",
      status: false,
      uploadImage: false,
    })
  },

  save() {
    this.saving = true

    const formData = new FormData()
    formData.append("body", this.mde.value())

    axios
      .post(url, formData)
      .then(() => {
        this.original = this.mde.value()
        this.$refs.ticketDescription.innerHTML = DOMPurify.sanitize(marked.parse(this.mde.value()))
        this.$data.editing = false
      })
      .finally(() => {
        this.saving = false
      })
  },

  cancel() {
    const formattedDescription = DOMPurify.sanitize(marked.parse(this.original))
    this.$refs.ticketDescription.innerHTML = formattedDescription
    this.$data.editing = false
  },
}))
