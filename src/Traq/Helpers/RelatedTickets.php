<?php
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

namespace Traq\Helpers;

use Avalon\Database;
use Traq\Models\Ticket;

/**
 * Links tickets together. Every relationship is stored in both directions.
 *
 * @author Jack P.
 * @package Traq
 * @subpackage Helpers
 * @since 3.9.0
 */
class RelatedTickets
{
    /**
     * Add a relationship between two tickets.
     *
     * @param Ticket $ticket The ticket to add the relationship to.
     * @param int $relatedTicketId The ID of the related ticket.
     * @param int $relationTypeId The type of relationship to add.
     * @param int $inverseRelationTypeId The type of inverse relationship to add.
     */
    public static function add(Ticket $ticket, int $relatedTicketId, int $relationTypeId, int $inverseRelationTypeId): void
    {
        static::insert($ticket->id, $relatedTicketId, $relationTypeId);
        static::insert($relatedTicketId, $ticket->id, $inverseRelationTypeId);
    }

    /**
     * Update a relationship between two tickets.
     *
     * @param Ticket $ticket The ticket to update the relationship for.
     * @param int $relatedTicketId The ID of the related ticket.
     * @param int $relationTypeId The type of relationship to update.
     * @param int $inverseRelationTypeId The type of inverse relationship to update.
     */
    public static function update(Ticket $ticket, int $relatedTicketId, int $relationTypeId, int $inverseRelationTypeId): void
    {
        static::setType($ticket->id, $relatedTicketId, $relationTypeId);
        static::setType($relatedTicketId, $ticket->id, $inverseRelationTypeId);
    }

    /**
     * Delete a relationship between two tickets.
     *
     * @param Ticket $ticket The ticket to delete the relationship for.
     * @param int $relatedTicketId The ID of the related ticket.
     */
    public static function delete(Ticket $ticket, int $relatedTicketId): void
    {
        static::remove($ticket->id, $relatedTicketId);
        static::remove($relatedTicketId, $ticket->id);
    }

    /**
     * Insert a relationship between two tickets.
     *
     * @param int $ticketId The ID of the ticket.
     * @param int $relatedTicketId The ID of the related ticket.
     * @param int $relationTypeId The type of relationship to insert.
     */
    private static function insert(int $ticketId, int $relatedTicketId, int $relationTypeId): void
    {
        $db = Database::connection();

        $db->prepare("INSERT INTO {$db->prefix}ticket_relationships (ticket_id, related_ticket_id, relation_type_id) VALUES (:ticketId, :relatedTicketId, :relationTypeId)")
            ->execute([
                'ticketId' => $ticketId,
                'relatedTicketId' => $relatedTicketId,
                'relationTypeId' => $relationTypeId
            ]);
    }

    /**
     * Set the type of relationship between two tickets.
     *
     * @param int $ticketId The ID of the ticket.
     * @param int $relatedTicketId The ID of the related ticket.
     * @param int $relationTypeId The type of relationship to set.
     */
    private static function setType(int $ticketId, int $relatedTicketId, int $relationTypeId): void
    {
        $db = Database::connection();

        $db->prepare("UPDATE {$db->prefix}ticket_relationships SET relation_type_id = :relationTypeId WHERE ticket_id = :ticketId AND related_ticket_id = :relatedTicketId")
            ->execute([
                'ticketId' => $ticketId,
                'relatedTicketId' => $relatedTicketId,
                'relationTypeId' => $relationTypeId
            ]);
    }

    /**
     * Remove a relationship between two tickets.
     *
     * @param int $ticketId The ID of the ticket.
     * @param int $relatedTicketId The ID of the related ticket.
     */
    private static function remove(int $ticketId, int $relatedTicketId): void
    {
        $db = Database::connection();

        $db->prepare("DELETE FROM {$db->prefix}ticket_relationships WHERE ticket_id = :ticketId AND related_ticket_id = :relatedTicketId")
            ->execute([
                'ticketId' => $ticketId,
                'relatedTicketId' => $relatedTicketId
            ]);
    }
}
