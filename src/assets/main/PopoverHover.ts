/*!
 * Traq
 * Copyright (C) 2009-2026 Jack Polgar
 * Copyright (C) 2012-2026 Traq.io
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

Alpine.data('popoverHover', (url: string) => ({
  open: false,
  html: null as string | null,
  el: undefined as HTMLDivElement | undefined,
  controller: undefined as AbortController | undefined,
  hideTimer: undefined as number | undefined,
  removeTimer: undefined as number | undefined,

  init() {
    this.$root.classList.add('popover-hover-container')
    this.$root.addEventListener('mouseenter', () => this.show())
    this.$root.addEventListener('mouseleave', () => this.hide())
  },

  async show() {
    window.clearTimeout(this.hideTimer)
    window.clearTimeout(this.removeTimer)

    if (!this.html) {
      this.controller?.abort()
      this.controller = new AbortController()

      try {
        const pathBits = url.split('?')
        const path = pathBits[0]
        const query = pathBits[1]
        const queryParams = new URLSearchParams(query)
        queryParams.set('popover', 'true')
        const newUrl = `${path}?${queryParams.toString()}`

        const resp = await fetch(newUrl, {
          signal: this.controller.signal,
        })

        if (!resp.ok) {
          return
        }

        this.html = await resp.text()
      } catch (e) {
        if ((e as Error).name !== 'AbortError') {
          console.error(e)
        }

        return
      }
    }

    if (!this.el) {
      const el = document.createElement('div')
      el.classList.add('popover', 'popover-hover')
      el.setAttribute('x-show', 'open')
      el.setAttribute('x-transition', '')
      el.innerHTML = this.html

      this.$root.append(el)
      this.el = el

      // Let Alpine pick up x-show/x-transition, then open so it animates in.
      Alpine.initTree(el)
      await this.$nextTick()
    }

    this.open = true
  },

  hide() {
    window.clearTimeout(this.hideTimer)
    this.hideTimer = window.setTimeout(() => {
      this.controller?.abort()
      this.open = false

      // Remove it once the leave transition has finished.
      this.removeTimer = window.setTimeout(() => {
        this.el?.remove()
        this.el = undefined
      }, 150)
    }, 200)
  },
}))
