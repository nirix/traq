<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
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

namespace Traq\Installer\Controllers;

/**
 * @author Jack P.
 * @since 4.0.0
 */
class Checks extends AppController
{
    /**
     * License agreement page.
     */
    public function licenseAgreementAction()
    {
        $this->title("License Agreement");

        // Get license
        $license = file_get_contents(dirname(dirname(dirname(__DIR__))) . '/COPYING');
        $this->set("license", $license);

        return $this->render("license_agreement.phtml");
    }
}
