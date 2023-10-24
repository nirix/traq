/*!
 * Traq
 * Copyright (C) 2009-2023 Jack Polgar
 * Copyright (C) 2012-2023 Traq.io
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

import type { Alpine } from 'alpinejs'
import type { EasyMDE } from 'easymde'

declare global {
  interface Window {
    Alpine: Alpine
    EasyMDE: EasyMDE
    traq: {
      base: string
      project_slug: string
    }
    language: {
      [key: string]: string
    }
  }
}
