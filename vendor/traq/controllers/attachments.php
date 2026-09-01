<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace traq\controllers;

use avalon\http\Request;
use avalon\http\Router;
use traq\models\Attachment;

/**
 * Attachments controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Attachments extends AppController
{
    private $attachment;

    // Before filters
    public $before = array(
        'view' => array('_check_permission'),
        'delete' => array('_check_permission')
    );

    /**
     * View attachment page
     *
     * @param integer $attachment_id
     */
    public function action_view($attachment_id)
    {
        // Don't try to load a view
        $this->render['view'] = false;

        $filename = str_replace(["\"", "\r", "\n"], '', (string) $this->attachment->name);
        $type = strtolower((string) $this->attachment->type);
        $content_type = explode('/', $type);
        $forceDownload = in_array($type, ['text/html', 'application/xhtml+xml', 'image/svg+xml'], true);

        if ($forceDownload) {
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
        } elseif ($content_type[0] == 'text' or $content_type[0] == 'image') {
            // If the mime-type is text, we can just display it
            // as plain text. I hate having to download files.
            if ($content_type[0] == 'text') {
                header("Content-type: text/plain");
            } else {
                header("Content-type: {$type}");
            }
            header("Content-Disposition: filename=\"{$filename}\"");
        }
        // Anything else should be downloaded
        else {
            header("Content-type: {$type}");
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
        }

        // Decode the contents and display it
        print(base64_decode($this->attachment->contents));
        exit;
    }

    /**
     * Delete attachment
     *
     * @param integer $attachment_id
     */
    public function action_delete($attachment_id)
    {
        // Delete and redirect
        $this->attachment->delete();
        Request::redirectTo($this->attachment->ticket->href());
    }

    /**
     * Used to check the permission for the requested action.
     */
    public function _check_permission($action)
    {
        // Get the attachment
        $this->attachment = Attachment::find(Router::$params[0]);

        // Check if the user has permission
        if (!current_user()->permission($this->attachment->ticket->project_id, "{$action}_attachments")) {
            // oh noes! display the no permission page.
            $this->show_no_permission();
            return false;
        }
    }
}
