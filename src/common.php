<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
 * Copyright (C) 2012-2015 Traq.io
 * https://github.com/nirix
 * https://traq.io
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

use Avalon\Helpers\HTML;
use Traq\Models\User;
use Traq\Models\Ticket;

/**
 * Shortcut for creating a query builder.
 */
function queryBuilder()
{
    return $GLOBALS['db']->createQueryBuilder();
}

/**
 * Get the setting value.
 *
 * @param string $setting
 *
 * @return mixed
 */
function setting($setting)
{
    static $settings;

    if (!$settings) {
        $rows = queryBuilder()->select('setting', 'value')->from(PREFIX . 'settings')->execute()->fetchAll();
        foreach ($rows as $row) {
            $settings[$row['setting']] = $row['value'];
        }
        unset($rows, $row);
    }

    return $settings[$setting];
}

/**
 * Get the current user information.
 *
 * @return array
 */
function current_user()
{
    return isset($GLOBALS['currentUser']) ? $GLOBALS['currentUser'] : null;
}

/**
 * Get the current project information.
 *
 * @return array
 */
function current_project()
{
    return isset($GLOBALS['currentProject']) ? $GLOBALS['currentProject'] : null;
}

/**
 * Check if the user has permission to perform the action.
 *
 * @param integer $projectId
 * @param string  $action
 *
 * @return boolean
 */
function has_permission($projectId, $action, $fetchProjectRoles = false)
{
    if (!$user = current_user()) {
        $user = anonymous_user();
    }

    return $user->hasPermission($projectId, $action, $fetchProjectRoles);
}

/**
 * Get the anonymous user.
 *
 * @return User
 */
function anonymous_user()
{
    static $anonymousUser;

    if (!$anonymousUser) {
        $anonymousUser = User::select('u.*', 'g.is_admin')
            ->leftJoin('u', PREFIX . 'usergroups', 'g', 'g.id = u.group_id')
            ->where('u.id = :id')
            ->setParameter('id', setting('anonymous_user_id'))
            ->fetch();
    }

    return $anonymousUser;
}

/**
 * Calculate percent.
 *
 * @param integer $min
 * @param integer $max
 * @param boolean $full
 *
 * @return mixed
 */
function get_percent($min, $max, $full = true)
{
    // Make sure we don't divide by zero and end the entire universe
    if ($min == 0 and $max == 0) {
        return 0;
    }

    $calculate = ($min / $max * 100);

    if ($full) {
        return $calculate;
    } else {
        $split = explode('.', $calculate);
        return $split[0];
    }
}

/**
 * Get a profile link with the users gravatar.
 *
 * @param  string  $userEmail
 * @param  string  $userName
 * @param  integer $userId
 * @param  integer $size
 *
 * @return string
 */
function gravatar_profile_link($userEmail, $userName, $userId, $size = null)
{
    return HTML::link(
        Gravatar::withString($userEmail, $userName, $size),
        routePath('user', ['id' => $userId])
    );
}

/**
 * Work out the locale for Moment.js
 *
 * @return string
 */
function moment_locale()
{
    $current = Avalon\Language::current()->locale;

    if (strlen($current) == 2) {
        return $current;
    } else {
        return substr($current, 0, 2) . '-' . substr($current, 2);
    }
}

/**
 * In no way is this a secure random hash, don't use it for security stuff.
 */
function random_hash()
{
    return sha1(microtime() . memory_get_usage() . time() . rand(0, 5000) . microtime(true));
}

/**
 * Get a query builder object pre-built with all the necessary ticket information.
 */
function ticketQuery()
{
    $ticket = Ticket::select(
        't.id',
        't.ticket_id',
        't.summary',
        't.user_id',
        't.milestone_id',
        't.version_id',
        't.component_id',
        't.type_id',
        't.status_id',
        't.priority_id',
        't.severity_id',
        't.assigned_to_id',
        't.votes',
        't.created_at',
        't.updated_at',
        'u.name AS user_name',
        'm.name AS milestone_name',
        'm.slug AS milestone_slug',
        'v.name AS version_name',
        'v.slug AS version_slug',
        'c.name AS component_name',
        'tp.name AS type_name',
        's.name AS status_name',
        'p.name AS priority_name',
        'sv.name AS severity_name',
        'at.name AS assigned_to_name'
    )
    ->from(PREFIX . 'tickets', 't')

    ->leftJoin('t', PREFIX . 'users', 'u', 'u.id = t.user_id')
    ->leftJoin('t', PREFIX . 'milestones', 'm', 'm.id = t.milestone_id')
    ->leftJoin('t', PREFIX . 'milestones', 'v', 'v.id = t.version_id')
    ->leftJoin('t', PREFIX . 'components', 'c', 'c.id = t.component_id')
    ->leftJoin('t', PREFIX . 'types', 'tp', 'tp.id = t.type_id')
    ->leftJoin('t', PREFIX . 'statuses', 's', 's.id = t.status_id')
    ->leftJoin('t', PREFIX . 'priorities', 'p', 'p.id = t.priority_id')
    ->leftJoin('t', PREFIX . 'severities', 'sv', 'sv.id = t.severity_id')
    ->leftJoin('t', PREFIX . 'users', 'at', 'at.id = t.assigned_to_id');

    $ticket->groupBy('t.id')
        ->addGroupBy('u.name')
        ->addGroupBy('m.name')
        ->addGroupBy('m.slug')
        ->addGroupBy('v.name')
        ->addGroupBy('v.slug')
        ->addGroupBy('c.name')
        ->addGroupBy('tp.name')
        ->addGroupBy('s.name')
        ->addGroupBy('p.name')
        ->addGroupBy('sv.name')
        ->addGroupBy('at.name');

    return $ticket;
}

/**
 * Dump and die
 */
function dd()
{
    echo "<pre>";
    call_user_func_array('var_dump', func_get_args());
    exit;
}
