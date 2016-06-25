<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

use Avalon\Language;
use Avalon\Helpers\Time;
use Avalon\Database\ConnectionManager;
use Traq\Models\Setting;
use Traq\Models\Ticket;
use Traq\Models\Project;
use Traq\Models\Permission;

/**
 * Shortcut for creating a query builder.
 */
function queryBuilder()
{
    return ConnectionManager::getConnection()->createQueryBuilder();
}

/**
 * Get setting.
 *
 * @param string $settingName
 *
 * @return mixed
 */
function setting($settingName)
{
    static $settings = [];

    if (!count($settings)) {
        foreach (Setting::all() as $setting) {
            $settings[$setting['setting']] = $setting['value'];
        }
    }

    return $settings[$settingName];
}

/**
 * Get the current project.
 *
 * @return \Traq\Models\Project
 */
function currentProject()
{
    return isset($GLOBALS['current_project']) ? $GLOBALS['current_project'] : null;
}

/**
 * Get current user.
 *
 * @return \Traq\Models\User
 */
function currentUser()
{
    return isset($GLOBALS['current_user']) ? $GLOBALS['current_user'] : null;
}

/**
 * Check users permission.
 *
 * @param string  $action
 * @param Project $project
 *
 * @return boolean
 */
function hasPermission($action, Project $project = null)
{
    // Admins can do everything, regardless of permissions.
    if (currentUser() && currentUser()->isAdmin()) {
        return true;
    }

    $permissions = $project ? Permission::getPermissions(currentUser(), $project)
                            : $GLOBALS['permissions'];

    return isset($permissions[$action]) ? $permissions[$action] : null;
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
function gravatarProfileLink($userEmail, $userName, $userId, $size = null)
{
    return HTML::link(
        Gravatar::withString($userEmail, $userName, $size),
        routePath('user', ['id' => $userId])
    );
}

/**
 * Returns the time ago in words with the 'ago' suffix.
 *
 * @param string $timestamp
 *
 * @return string
 */
function timeAgoInWords($timestamp)
{
    return $timestamp
        ? Language::translate('time.x_ago', [Time::agoInWords($timestamp)])
        : null;
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
function getPercent($min, $max, $full = true)
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
 * "Shortcut" for `$cond ? $true : $false`
 *
 * @param boolean $cond
 * @param mixed   $true
 * @param mixed   $false
 *
 * @return mixed
 */
function iif($cond, $true, $false = null)
{
    return $cond ? $true : $false;
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
    );

    $ticket->leftJoin('t', PREFIX . 'users', 'u', 'u.id = t.user_id')
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
