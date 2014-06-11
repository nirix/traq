<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

namespace traq\helpers;

use avalon\http\Request;

/**
 * Notification helper.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Helpers
 */
class Pagination
{
    public $paginate = false;
    public $rows = 0;
    public $per_page = 25;
    public $next_page;
    public $prev_page;
    public $next_page_url;
    public $prev_page_url;
    public $limit;

    /**
     * Generates pagination information.
     *
     * @param integer $page     Current page
     * @param integer $per_page Rows per page
     * @param integer $rows     Rows in the database
     */
    public function __construct($page, $per_page, $rows)
    {
        // Set information
        $this->page = $page;
        $this->per_page = $per_page;
        $this->total_pages = ceil($rows / $per_page);
        $this->rows = $rows;

        // More than per-page limit?
        if ($rows > $per_page) {
            $this->paginate = true;

            // Next/prev pages
            $this->next_page = ($this->page + 1);
            $this->prev_page = ($this->page - 1);

            // Limit pages
            $this->limit = ($this->page-1 > 0 ? $this->page-1 : 0) * $per_page;

            // Get correct request URI with page number
            if (!isset(Request::$request['page'])) {
                $request_uri = Request::requestUri() . (strlen($_SERVER['QUERY_STRING']) ? '&amp;' : '?') . "page={$this->page}";
            } else {
                $request_uri = Request::requestUri();
            }

            // Next page URL
            if ($this->next_page <= $this->total_pages) {
                $this->next_page_url = str_replace("page=" . $this->page, "page=" . $this->next_page, $request_uri);
            }

            // Previous page URL
            if ($this->prev_page > 0) {
                $this->prev_page_url = str_replace("page=" . $this->page, "page=" . $this->prev_page, $request_uri);
            }
        }
    }
}
