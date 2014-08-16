<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Traq.io
 * Copyright (C) 2009-2014 Jack Polgar
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

use Radium\Helpers\HTML;
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
     * Returns the HTML for the users garavar.
     *
     * @param \Traq\Models\User $user
     * @param integer          $size Size of the gravatar
     */
    public static function forUser(User $user, $size = null)
    {
        $hash = md5($user->email);
        $url = "https://www.gravatar.com/avatar/{$hash}";

        if ($size) {
            $url = "{$url}?s={$size}}";
        }

        return HTML::image($url);
    }
}
