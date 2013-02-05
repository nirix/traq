<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

use traq\models\Subscription;

/**
 * Checks if the user is subscribed to the
 * passed object.
 *
 * @param object $user
 * @param object $object
 */
function is_subscribed($user, $object)
{
    $class = new ReflectionClass(get_class($object));
    $type = strtolower($class->getShortName());

    $sub = Subscription::select()->where(array(
        array('project_id', ($type == 'project') ? $object->id : $object->project_id),
        array('user_id', $user->id),
        array('type', $type),
        array('object_id', $object->id)
    ))->exec()->fetch();

    return $sub !== false;
}

/**
 * Renders the subscription link
 * for the passed object.
 *
 * @param object $object
 */
function subscription_link_for($object)
{
    // Do nothing if the user is not logged in.
    if (!LOGGEDIN) {
        return false;
    }

    $class = new ReflectionClass(get_class($object));
    return View::render('subscriptions/_subscribe', array('type' => strtolower($class->getShortName()), 'object' => $object));
}
