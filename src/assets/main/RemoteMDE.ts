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
import { marked, Renderer } from 'marked'

function isSafeUrl(href: string | null): boolean {
  if (!href) {
    return false
  }

  return !/^\s*(javascript|vbscript|data):/i.test(href)
}

function renderMarkdown(source: string): string {
  const escaped = source.replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
  const renderer = new Renderer()
  const link = renderer.link.bind(renderer)
  const image = renderer.image.bind(renderer)

  renderer.link = (href, title, text) => {
    if (!isSafeUrl(href)) {
      return text
    }

    return link(href, title, text)
  }

  renderer.image = (href, title, text) => {
    if (!isSafeUrl(href)) {
      return text
    }

    return image(href, title, text)
  }

  return marked.parse(escaped, { renderer }) as string
}

// @ts-ignore ignore this
Alpine.data('remoteMde', ({ url, height }) => ({
  saving: false,
  original: '',

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
    formData.append('body', this.mde.value())

    axios
      .post(url, formData)
      .then(() => {
        this.original = this.mde.value()
        this.$refs.ticketDescription.innerHTML = renderMarkdown(this.mde.value())
        this.$data.editing = false
      })
      .finally(() => {
        this.saving = false
      })
  },

  cancel() {
    this.$refs.ticketDescription.innerHTML = renderMarkdown(this.original)
    this.$data.editing = false
  },
}))
