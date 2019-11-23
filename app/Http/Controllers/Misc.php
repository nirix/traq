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

namespace Traq\Controllers;

use Avalon\Http\Controller;
use Traq\Models\Status;
use Traq\Models\Priority;

/**
 * Misc. actions controller.
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 3.0.0
 */
class Misc extends Controller
{
    protected $layout = null;

    /**
     * JavaScript route for setting things like translation strings used by the popover-confirm.
     */
    public function jsAction()
    {
        $resp = $this->render('misc/js.php');
        $resp->contentType = "application/javascript";
        return $resp;
    }

    /**
     * Ticket statuses.
     */
    public function statusesAction()
    {
        return $this->jsonResponse(Status::select()->execute()->fetchAll());
    }

    /**
     * Ticket priorities.
     */
    public function prioritiesAction()
    {
        return $this->jsonResponse(Priority::select()->execute()->fetchAll());
    }
}
