<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
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

use ReflectionClass;
use Avalon\Templating\View;
use Traq\Models\Subscription as SubscriptionModel;

class Subscription
{
    /**
     * Checks if the user is subscribed to the
     * passed object.
     *
     * @param object $user
     * @param object $object
     */
    public static function isSubscribed($user, $object)
    {
        $class = new ReflectionClass(get_class($object));
        $type = strtolower($class->getShortName());

        $sub = SubscriptionModel::select()
            ->where('project_id = ?', ($type == 'project') ? $object->id : $object->project_id)
            ->andWhere('user_id = ?', $user->id)
            ->andWhere('type = ?', $type)
            ->andWhere('object_id = ?', $object->id)
            ->fetch();

        return $sub !== false;
    }

    /**
     * Renders the subscription link
     * for the passed object.
     *
     * @param object $object
     */
    public static function linkFor($object)
    {
        $class = new ReflectionClass(get_class($object));
        return View::render('subscriptions/_subscribe.phtml', [
            'type'   => strtolower($class->getShortName()),
            'object' => $object
        ]);
    }
}
