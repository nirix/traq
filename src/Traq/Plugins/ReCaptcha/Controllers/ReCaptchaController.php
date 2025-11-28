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

namespace ReCaptcha\Controllers;

use Avalon\Database;
use Avalon\Http\Request;

/**
 * reCaptcha controller.
 *
 * @author Jack P.
 * @since 3.9
 * @package SecurityQuestions
 * @subpackage Controllers
 */
class ReCaptchaController extends \Traq\Controllers\Admin\AppController
{
    /**
     * Question management page.
     */
    public function index()
    {
        if (Request::method() == 'POST') {
            $site_key = Request::$post['site_key'];
            $secret_key = Request::$post['secret_key'];

            Database::connection()->update('settings')->set(['value' => $site_key])->where('setting', 'recaptcha_site_key')->exec();
            Database::connection()->update('settings')->set(['value' => $secret_key])->where('setting', 'recaptcha_secret_key')->exec();

            return $this->redirectTo('/admin/settings/recaptcha');
        }

        // Set page title
        $this->title(l('recaptcha'));

        $data = [
            'site_key' => settings('recaptcha_site_key'),
            'secret_key' => settings('recaptcha_secret_key'),
        ];

        return $this->render('recaptcha/index.phtml', $data);
    }
}
