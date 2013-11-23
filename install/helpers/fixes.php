<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

namespace Installer\Helpers;

use Traq\Models\Attachment;
use Traq\Models\Setting;
use Traq\Models\Ticket;
use Traq\Models\TicketHistory;
use Traq\Models\Timeline;
use Traq\Models\User;

/**
 * Fixes library with various fixes for Traq.
 *
 * @since 3.3
 */
class Fixes
{
    /**
     * Fixes database rows that belong to a user that has since
     * been deleted by moving ownership to the Anonymous user.
     */
    public static function deletedUsers()
    {
        global $db;

        $anonymous_user_id = Setting::find('setting', 'anonymous_user_id')->value;

        // Fix attachments
        $attachment_ids = array();
        foreach (Attachment::fetch_all() as $model) {
            if (!User::find($model->user_id)) {
                $attachment_ids[] = $model->id;
            }
        }

        $db->query("UPDATE `{$db->prefix}attachments` SET `user_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $attachment_ids) . ")");

        // Fix tickets
        $ticket_ids = array();
        $assigned_ticket_ids = array();
        foreach (Ticket::fetch_all() as $model) {
            if (!User::find($model->user_id)) {
                $ticket_ids[] = $model->id;
            }

            if ($model->assigned_to_id != 0 and !User::find($model->assigned_to_id)) {
                $assigned_ticket_ids[] = $model->id;
            }
        }

        $db->query("UPDATE `{$db->prefix}tickets` SET `user_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $ticket_ids) . ")");
        $db->query("UPDATE `{$db->prefix}tickets` SET `assigned_to_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $assigned_ticket_ids) . ")");

        // Fix ticket history
        $history_ids = array();
        foreach (TicketHistory::fetch_all() as $model) {
            if (!User::find($model->user_id)) {
                $history_ids[] = $model->id;
            }
        }

        $db->query("UPDATE `{$db->prefix}ticket_history` SET `user_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $history_ids) . ")");

        // Fix timeline
        $timeline_ids = array();
        foreach (Timeline::fetch_all() as $model) {
            if (!User::find($model->user_id)) {
                $timeline_ids[] = $model->id;
            }
        }

        $db->query("UPDATE `{$db->prefix}timeline` SET `user_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $timeline_ids) . ")");
    }
}
