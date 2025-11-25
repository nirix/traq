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

Alpine.data('dropdown', () => ({
  open: false,

  init() {
    this.open = false

    const button = this.$refs.dropdownToggle as HTMLButtonElement
    const dropdown = this.$refs.dropdownMenu as HTMLDivElement

    // if the dropdown menu is going off the right side of the screen, move it to the left
    if (dropdown.offsetLeft + dropdown.offsetWidth > window.innerWidth) {
      // set the dropdown top right corner to the bottom right corner of the button
      // dropdown.style.top = `${button.offsetTop + button.offsetHeight}px`
      const rect = button.getBoundingClientRect()
      dropdown.style.left = `${rect.right - dropdown.offsetWidth}px`
      // dropdown.style.top = `${rect.top}px`
    }
  },

  toggle() {
    this.open = !this.open
  },
}))
