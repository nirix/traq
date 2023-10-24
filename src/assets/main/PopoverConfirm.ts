/*!
 * Traq
 * Copyright (C) 2009-2022 Jack Polgar
 * Copyright (C) 2012-2022 Traq.io
 * https://github.com/nirix
 * http://traq.io
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

import Alpine from 'alpinejs'
import axios from 'axios'

// @ts-ignore: single-line error better than entire file /shrug
Alpine.data('popoverConfirm', ({ position, message, success, remote, post }) => ({
  open: false,
  loading: false,

  init() {
    const el = document.createElement('div')
    el.id = 'popover-confirm'
    el.classList.add('popover-confirm')
    this.el = el

    const messageEl = document.createElement('div')
    messageEl.innerHTML = message ?? 'Are you sure?'
    messageEl.classList.add('popover-confirm-content')

    const actionsEl = document.createElement('div')
    actionsEl.classList.add('popover-confirm-actions')

    const cancelBtn = document.createElement('button')
    cancelBtn.classList.add('btn')
    cancelBtn.textContent = window.language.no
    cancelBtn.addEventListener('click', () => {
      this.open = false
    })

    this.acceptBtn = document.createElement('button')
    this.acceptBtn.classList.add('btn-danger')
    this.acceptBtn.textContent = window.language.yes
    this.acceptBtn.addEventListener('click', () => {
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
        const form = document.createElement('form')
        form.setAttribute('method', 'post')
        form.setAttribute('action', post)
        form.id = 'popover-confirm-post-form'
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

    this.$watch('open', () => {
      if (this.open) {
        this.openPopover()
        return
      }

      this.closePopover()
    })

    this.$watch('loading', () => {
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

    this.resizeEvent = window.addEventListener('resize', () => {
      this.position()
    })
  },

  position() {
    const rootRect = this.$root.getBoundingClientRect()
    const rect = this.el.getBoundingClientRect()
    const top = rootRect.y + rootRect.height + 5 + window.scrollY

    let right
    if (position === 'center') {
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
    window.removeEventListener('resize', this.resizeEvent)
    this.el.remove()
  },
}))
