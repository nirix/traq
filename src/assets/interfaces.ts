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

import type { DateTime } from 'luxon'

export interface FilterInterface {
  field: string
  condition: boolean
  values: string[]
}

export interface MilestoneInterface {
  id: number
  name: string
  slug: string
  project: ProjectInterface
}

export interface ComponentInterface {
  id: number
  name: string
}

export interface StatusInterface {
  id: number
  name: string
}

export interface PriorityInterface {
  id: number
  name: string
}

export interface SeverityInterface {
  id: number
  name: string
}

export interface TypeInterface {
  id: number
  name: string
}

export interface UserInterface {
  id: number
  name: string
}

export interface CustomFieldInterface {
  id: number
  name: string
  slug: string
  values: string[]
  type: string
}

export interface TicketInterface {
  ticket_id: number
  summary: string
  user_id: number
  assigned_to_id: number | null
  votes: number
  created_at: string
  updated_at: string | null
  is_closed: boolean
  owner: string
  assignee: string | null
  type: string
  milestone: string
  milestone_slug: string
  version: string | null
  version_slug: string | null
  component: string | null
  status: string
  priority: string
  priority_id: number
  severity: string
}

export interface ProjectInterface {
  name: string
  slug: string
}
