<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

namespace Traq\Middleware;

use Attribute;
use Avalon\Http\Middleware\MiddlewareInterface;
use Avalon\Http\Request;
use Avalon\Http\Response;
use Avalon\Output\View;
use Traq\Locale;
use Traq\Models\User;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class UserMiddleware implements MiddlewareInterface
{
    protected ?User $user = null;
    protected bool $loggedin = false;

    public function run(callable $next): Response
    {
        $this->loadUser();

        Request::set('current_user', $this->user);
        Request::set('loggedin', $this->loggedin);

        // Legacy support
        // TODO: Remove this
        define("LOGGEDIN", $this->loggedin);

        return $next();
    }

    private function getApiKey(): ?string
    {
        $authHeader = $_SERVER['HTTP_X_API_KEY'] ?? null;

        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return trim(substr($authHeader, 7));
        }

        return $authHeader;
    }

    /**
     * Does the checking for the session cookie and fetches the users info.
     *
     * @author Jack P.
     * @since 3.0
     * @access private
     */
    private function loadUser(): void
    {
        global $locale;

        // Check if the session cookie is set, if so, check if it matches a user
        // and set set the user info.
        if (isset($_COOKIE['_traq']) && $user = User::find('login_hash', $_COOKIE['_traq'])) {
            $this->user = $user;
        }
        // Check if the API key is set
        else {
            $apiKey = $this->getApiKey();

            if ($apiKey) {
                $this->user = User::find('api_key', $apiKey);
                Request::set('is_api', true);
            }
        }

        // If a user was found, load their language
        if ($this->user) {
            // Load user's locale
            if ($this->user->locale != '') {
                $user_locale = Locale::load($this->user->locale);
                if ($user_locale) {
                    $locale = $user_locale;
                }
            }

            $this->loggedin = true;
        }
        // Otherwise just set the user info to guest.
        else {
            $this->user = new User(array(
                'id' => settings('anonymous_user_id'),
                'username' => l('guest'),
                'group_id' => 3
            ));

            $this->loggedin = false;
        }

        // Set the current_user variable in the views.
        View::set('current_user', $this->user);
    }
}
