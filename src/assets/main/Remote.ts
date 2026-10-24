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

Alpine.data('remote', ({ target, method, onSuccess }: { target?: string; method?: string; onSuccess: (resp: unknown, el: HTMLElement) => void }) => ({
  init() {
    this.$el.addEventListener('click', (event: Event) => {
      event.preventDefault()
      this.send()
    })
  },

  send() {
    const url = target ?? this.$el.getAttribute('href')
    if (!url) {
      return
    }

    // Use fetch to get the response
    fetch(url, {
      method: method ?? 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
    }).then(async (resp) => {
      if (resp.ok) {
        const data = await resp.json()
        onSuccess(data, this.$el)
      } else {
        console.error(resp.statusText)
      }
    })
  },
}))
