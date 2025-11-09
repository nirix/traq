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
import { marked } from 'marked'

interface IRemoteMDEStore {
  saving: boolean
  original: string
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

Alpine.data('remoteMde', ({ url, height }) => ({
  saving: false,
  original: '',
  mde: null as EasyMDE | null,

  init() {
    this.original = this.$refs.editor.value

    this.mde = new window.EasyMDE({
      element: this.$refs.editor,
      autoDownloadFontAwesome: false,
      minHeight: height ?? '150px',
      status: false,
      uploadImage: false,
    })
  },

  save() {
    this.saving = true

    const formData = new FormData()
    formData.append('body', this.mde?.value() ?? '')

    axios
      .post(url, formData)
      .then(async () => {
        this.original = this.mde?.value() ?? ''
        this.$refs.ticketDescription.innerHTML = await marked.parse(this.mde?.value()?.replaceAll('<', '&lt;').replaceAll('>', '&gt;') ?? '')
        this.$data.editing = false
      })
      .finally(() => {
        this.saving = false
      })
  },

  cancel() {
    const formattedDescription = marked.parse(this.original.replaceAll('<', '&lt;').replaceAll('>', '&gt;'))
    this.$refs.ticketDescription.innerHTML = formattedDescription.toString()
    this.$data.editing = false
  },
} as IRemoteMDEStore))
