<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Traq.io
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

        $anon_user_id_setting = $db->query("SELECT * FROM `settings` WHERE `setting` = 'anonymous_user_id' LIMIT 1");
        $anonymous_user_id = $anon_user_id_setting['value'];

        // Fix attachments
        $attachment_ids = array(0);
        foreach (static::fetch_all('attachments') as $row) {
            if (!static::fetch_user($row['user_id'])) {
                $attachment_ids[] = $row['id'];
            }
        }

        $db->query("UPDATE `{$db->prefix}attachments` SET `user_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $attachment_ids) . ")");

        // Fix tickets
        $ticket_ids = array(0);
        $assigned_ticket_ids = array();
        foreach (static::fetch_all('tickets') as $row) {
            if (!static::fetch_user($row['user_id'])) {
                $ticket_ids[] = $row['id'];
            }

            if ($row['assigned_to_id'] != 0 and !static::fetch_user($row['assigned_to_id'])) {
                $assigned_ticket_ids[] = $row['id'];
            }
        }

        $db->query("UPDATE `{$db->prefix}tickets` SET `user_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $ticket_ids) . ")");
        $db->query("UPDATE `{$db->prefix}tickets` SET `assigned_to_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $assigned_ticket_ids) . ")");

        // Fix ticket history
        $history_ids = array(0);
        foreach (static::fetch_all('ticket_history') as $row) {
            if (!static::fetch_user($row['user_id'])) {
                $history_ids[] = $row['id'];
            }
        }

        $db->query("UPDATE `{$db->prefix}ticket_history` SET `user_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $history_ids) . ")");

        // Fix timeline
        $timeline_ids = array(0);
        foreach (static::fetch_all('timeline') as $row) {
            if (!static::fetch_user($row['user_id'])) {
                $timeline_ids[] = $row['id'];
            }
        }

        $db->query("UPDATE `{$db->prefix}timeline` SET `user_id` = '{$anonymous_user_id}' WHERE `id` IN (" . implode(',', $timeline_ids) . ")");
    }

    /**
     * Fetches the user by the ID.
     *
     * @param integer $id
     *
     * @return mixed
     */
    private static function fetch_user($id)
    {
        global $db;

        if ($user = $db->query("SELECT * FROM `{$db->prefix}users` WHERE `id` = '{$id}' LIMIT 1")) {
            return $user->fetch();
        }

        return false;
    }

    /**
     * Fetches all the rows for the specified table.
     *
     * @param string $table
     *
     * @return array
     */
    private static function fetch_all($table)
    {
        global $db;
        return $db->query("SELECT * FROM `{$db->prefix}{$table}`")->fetchAll();
    }
}
