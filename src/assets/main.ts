import "./css/main.css"
import "./main/FontAwesome"
import EasyMDE from "easymde"

window.EasyMDE = EasyMDE

// Alpinejs for simpler UI tasks
import Alpine from "alpinejs"

import "./main/PopoverConfirm"
import "./main/TicketTasks"
import "./main/RemoteMDE"
import "./main/EasyMDE"
import "./main/NavbarSearch"

window.Alpine = Alpine
Alpine.start()
