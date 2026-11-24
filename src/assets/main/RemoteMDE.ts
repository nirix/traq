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
import { sanitiseHtml } from './sanitiseHtml'

interface IRemoteMDEStore {
  saving: boolean
  original: string
  originalHtml: string
  init: () => void
  save: () => void
  cancel: () => void
  mde: EasyMDE | null
  $refs: {
    editor: HTMLTextAreaElement
    ticketDescription: HTMLDivElement
  }
  $data: {
    editing: boolean
  }
}

Alpine.data(
  'remoteMde',
  ({ url, height }) =>
    ({
      saving: false,
      original: '',
      originalHtml: '',
      mde: null as EasyMDE | null,

      init() {
        this.original = this.$refs.editor.value
        this.originalHtml = this.$refs.ticketDescription.innerHTML

        this.mde = new window.EasyMDE({
          element: this.$refs.editor,
          autoDownloadFontAwesome: false,
          minHeight: height ?? '150px',
          status: false,
          uploadImage: false,
          renderingConfig: { sanitizerFunction: sanitiseHtml },
        })
      },

      save() {
        this.saving = true

        fetch(url, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ body: this.mde?.value() ?? '' }),
        })
          .then(async (resp) => {
            if (!resp.ok) {
              console.error(resp.statusText)
              return
            }

            // The server renders and sanitises the markdown; never render it client-side.
            const data: { body: string; html: string } = await resp.json()
            this.original = data.body
            this.originalHtml = data.html
            this.$refs.ticketDescription.innerHTML = data.html
            this.$data.editing = false
          })
          .finally(() => {
            this.saving = false
          })
      },

      cancel() {
        this.$refs.ticketDescription.innerHTML = this.originalHtml
        this.$data.editing = false
      },
    } as IRemoteMDEStore),
)
