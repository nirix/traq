import "./css/main.css"

// Alpinejs for simpler UI tasks
import Alpine from "alpinejs"
window.Alpine = Alpine
Alpine.start()

// FontAwesome
import { library, dom } from "@fortawesome/fontawesome-svg-core"
import { faAngleDoubleRight, faPencil, faTrash, faEye, faEyeSlash } from "@fortawesome/free-solid-svg-icons"

library.add(faAngleDoubleRight)
library.add(faPencil)
library.add(faTrash)
library.add(faEye)
library.add(faEyeSlash)
dom.watch()
