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

/**
 * Sanitises the HTML the editor preview renders from markdown.
 *
 * The server never renders raw HTML from markdown (it escapes it), so the preview shouldn't
 * either. Anything that isn't plain markdown output is shown as text, like the server does.
 */

const ALLOWED_TAGS = new Set([
  'a',
  'blockquote',
  'br',
  'code',
  'del',
  'em',
  'h1',
  'h2',
  'h3',
  'h4',
  'h5',
  'h6',
  'hr',
  'img',
  'input',
  'li',
  'ol',
  'p',
  'pre',
  'strong',
  'table',
  'tbody',
  'td',
  'th',
  'thead',
  'tr',
  'ul',
])

const ALLOWED_ATTRIBUTES: Record<string, string[]> = {
  a: ['href', 'title'],
  img: ['src', 'alt', 'title'],
  code: ['class'],
  input: ['type', 'checked', 'disabled'],
  ol: ['start'],
  td: ['align'],
  th: ['align'],
}

const URL_ATTRIBUTES = new Set(['href', 'src'])

function isSafeUrl(value: string): boolean {
  // Browsers ignore whitespace and control characters inside the scheme.
  // eslint-disable-next-line no-control-regex
  const url = value.replace(/[\u0000-\u0020\u007f]+/g, '')

  // No scheme means it's relative, otherwise it has to be one of these.
  return !/^[a-z][a-z0-9+.-]*:/i.test(url) || /^(https?|mailto):/i.test(url)
}

function isSafeAttribute(tag: string, name: string, value: string): boolean {
  if (!(ALLOWED_ATTRIBUTES[tag] ?? []).includes(name)) {
    return false
  }

  if (URL_ATTRIBUTES.has(name)) {
    return isSafeUrl(value)
  }

  if (tag === 'code' && name === 'class') {
    return /^language-[\w-]+$/.test(value)
  }

  if (tag === 'input' && name === 'type') {
    return value === 'checkbox'
  }

  return true
}

function clean(parent: Element): void {
  for (const child of Array.from(parent.children)) {
    const tag = child.tagName.toLowerCase()

    if (!ALLOWED_TAGS.has(tag)) {
      // Show it as text instead of dropping it.
      child.replaceWith(parent.ownerDocument.createTextNode(child.outerHTML))
      continue
    }

    for (const attribute of Array.from(child.attributes)) {
      if (!isSafeAttribute(tag, attribute.name, attribute.value)) {
        child.removeAttribute(attribute.name)
      }
    }

    clean(child)
  }
}

export function sanitiseHtml(html: string): string {
  // DOMParser documents are inert, nothing in here runs or loads while it's being cleaned.
  const doc = new DOMParser().parseFromString(html, 'text/html')
  clean(doc.body)

  return doc.body.innerHTML
}
