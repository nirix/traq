/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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
import type EasyMDE from 'easymde'

type TicketTemplatesType = {
  ['mde-instance']?: string
}

Alpine.directive('ticket-template', (el, { expression }, { evaluate }) => {
  try {
    const options: TicketTemplatesType = expression ? (evaluate(expression) as TicketTemplatesType) : {}

    if (!options['mde-instance']) {
      return
    }

    el.addEventListener('change', () => {
      const instanceKey = `mde-${options['mde-instance']}` as keyof typeof window
      const mde = window[instanceKey] as EasyMDE
      if (!mde) {
        return
      }

      axios.get(window.traq.base + '_ajax/ticket_template/' + (el as HTMLInputElement).value).then((resp) => {
        mde.value(resp.data)
      })
    })
  } catch {
    console.error('Unable to initialise ticket templates')
  }
})
