<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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

class TicketsController extends AppController
{
	public function action_index()
	{
		Load::helper('tickets');
		
		$where = array();
		$where[] = array("project_id = '?'", $this->project->id);
		
		$sql = array();
		
		// Filters
		foreach (Request::$request as $filter => $value) {
			if (!in_array($filter, ticket_filters())) {
				continue;
			}
			$filter_sql = array();
			// Milestone filter
			if ($filter == 'milestone') {
				foreach (explode(',', $value) as $name) {
					$milestone = Milestone::find('slug', $name);
					$filter_sql[] = $milestone->id;
				}
				$sql[] = "milestone_id IN (" . implode(', ', $filter_sql) . ")";
			}
			// Status filter
			elseif ($filter == 'status') {
				foreach (explode(',', $value) as $name) {
					$status = TicketStatus::find('name', urldecode($name));
					$filter_sql[] = $status->id;
				}
				$sql[] = "status_id IN (" . implode(', ', $filter_sql) . ")";
			}
			// Type filter
			elseif ($filter == 'type') {
				foreach (explode(',', $value) as $name) {
					$type = TicketType::find('name', urldecode($name));
					$filter_sql[] = $type->id;
				}
				$sql[] = "type_id IN (" . implode(', ', $filter_sql) . ")";
			}
			// Component filter
			elseif ($filter == 'component') {
				foreach (explode(',', $value) as $name) {
					$component = Component::find('name', urldecode($name));
					$filter_sql[] = $component->id;
				}
				$sql[] = "component_id IN (" . implode(', ', $filter_sql) . ")";
			}
		}
		
		// Fetch tickets
		$tickets = array();
		$rows = $this->db->select()->from('tickets')->customSql(count($sql) > 0 ? 'WHERE ' . implode(' AND ', $sql) :'')->exec()->fetchAll();
		foreach($rows as $row) {
			$tickets[] = new Ticket($row);
		}
		
		View::set('tickets', $tickets);
	}
	
	public function action_view($ticket_id)
	{
		$ticket = Ticket::find('ticket_id', $ticket_id);
		View::set('ticket', $ticket);
	}
}