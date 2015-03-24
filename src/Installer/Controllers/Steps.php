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
class Steps extends AppController
{
    /**
     * Database information form.
     */
    public function databaseInformationAction()
    {
        $this->title("Database Information");
        return $this->render("steps/database_information.phtml");
    }

    /**
     * Admin account information form.
     */
    public function accountInformationAction()
    {
        $databaseInfo = $this->checkDatabaseInformation();

        if ($databaseInfo) {
            return $databaseInfo;
        }

        $this->title("Admin Account");
        return $this->render("steps/account_information.phtml");
    }

    /**
     * Confirm information
     */
    public function confirmInformationAction()
    {
        $accountInfo = $this->checkAccountInformation();

        if ($accountInfo) {
            return $accountInfo;
        }

        $this->title("Confirm Information");
        return $this->render("steps/confirm_information.phtml");
    }
}
