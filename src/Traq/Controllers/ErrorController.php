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

namespace Traq\Controllers;

use avalon\http\Request;
use Avalon\Http\Response;
use Traq\Controllers\AppController;

/**
 * Error controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ErrorController extends AppController
{
    public function error404(): Response
    {
        $response = $this->render('error/404', [
            'request' => Request::requestUri(),
        ]);
        $response->setStatusCode(404);

        return $response;
    }
}
