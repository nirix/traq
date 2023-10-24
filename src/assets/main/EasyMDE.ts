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
import EasyMDE from 'easymde'

type EasyMDEType = {
  height?: string
}

Alpine.directive('mde', (el, { value, modifiers, expression }, { Alpine, effect, cleanup, evaluate }) => {
  try {
    const options: EasyMDEType = expression ? (evaluate(expression) as EasyMDEType) : {}

    // Don't initialise unless we need to.
    if (el.dataset['mded'] === 'yes') {
      return
    }
    el.dataset['mded'] = 'yes'

    const mde = new EasyMDE({
      element: el,
      autoDownloadFontAwesome: false,
      minHeight: options.height ?? '150px',
      status: false,
      uploadImage: false,
    })

    if (el.name) {
      window[`mde-${el.name}`] = mde
    }
  } catch {
    console.error('Unable to initialise EasyMDE')
  }
})
