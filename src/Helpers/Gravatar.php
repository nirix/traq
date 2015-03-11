<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Traq.io
 * Copyright (C) 2009-2015 Jack Polgar
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Traq\Helpers;

use Avalon\Helpers\HTML;
use Traq\Models\User;

/**
 * Gravatar helper
 *
 * @author Jack P.
 * @package Traq\Helpers
 * @since 4.0
 */
class Gravatar
{
    /**
     * Generates the gravatar URL.
     *
     * @param string  $email
     * @param integer $size  Image size
     */
    public static function url($email, $size = null)
    {
        $hash = md5($email);
        $url = "https://www.gravatar.com/avatar/{$hash}";

        if ($size) {
            $url = "{$url}?s={$size}";
        }

        return $url;
    }

    /**
     * Returns the HTML for the users avatar.
     *
     * @param \Traq\Models\User $user
     * @param integer           $size Image size
     */
    public static function forUser(User $user, $size = null)
    {
        return HTML::image(static::url($user->email, $size));
    }

    /**
     * Returns the avatar with the users username on the right.
     *
     * @param \Traq\Models\User $user
     * @param integer           $size Image size
     */
    public static function withUsername(User $user, $size = null)
    {
        return static::forUser($user, $size) . " {$user->username}";
    }
}
