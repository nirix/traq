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
     * Raster types that are safe to display inline. SVG, HTML, XML, and
     * anything else is downloaded as octet-stream so the stored MIME type
     * cannot be used for XSS or header injection.
     */
    private const INLINE_IMAGE_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/avif',
    ];

    /**
     * View attachment page
     *
     * @param integer $attachment_id
     */
    public function action_view($attachment_id)
    {
        // Don't try to load a view
        $this->render['view'] = false;

        $filename = $this->downloadFilename((string) $this->attachment->name);
        $contents = base64_decode((string) $this->attachment->contents);
        $mediaType = $this->normalizedMediaType((string) $this->attachment->type);
        $inlineType = $this->inlineContentType($mediaType, $contents);

        header('X-Content-Type-Options: nosniff');
        header("Content-Security-Policy: default-src 'none'; sandbox");
        header('X-Frame-Options: DENY');

        if ($inlineType !== null) {
            header("Content-Type: {$inlineType}");
            header("Content-Disposition: inline; filename=\"{$filename}\"");
        } else {
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
        }

        print($contents);
        exit;
    }

    /**
     * Strip quotes and control characters so the name is safe in Content-Disposition.
     */
    private function downloadFilename(string $name): string
    {
        $name = str_replace(["\\", '"', "\r", "\n", "\0"], '', $name);
        $name = preg_replace('/[\x00-\x1f\x7f]/', '', $name) ?? '';
        $name = trim($name);

        return $name !== '' ? $name : 'attachment';
    }

    /**
     * Return type/subtype only. Parameters, whitespace, and invalid tokens are dropped.
     */
    private function normalizedMediaType(string $raw): string
    {
        $raw = strtolower(str_replace(["\r", "\n", "\0"], '', $raw));
        $raw = trim(explode(';', $raw, 2)[0]);

        if (!preg_match('/^[a-z0-9][a-z0-9.+-]*\/[a-z0-9][a-z0-9.+-]*$/', $raw)) {
            return '';
        }

        return $raw;
    }

    /**
     * Content-Type to send for an inline response, or null to force download.
     */
    private function inlineContentType(string $mediaType, string $contents): ?string
    {
        if ($mediaType !== '' && str_starts_with($mediaType, 'image/') && $this->looksLikeSvg($contents)) {
            return null;
        }

        if (in_array($mediaType, self::INLINE_IMAGE_TYPES, true)) {
            return $mediaType;
        }

        if (str_starts_with($mediaType, 'text/')) {
            $dangerousText = [
                'text/html',
                'text/xml',
                'text/xsl',
                'text/javascript',
                'text/ecmascript',
            ];

            if (in_array($mediaType, $dangerousText, true)) {
                return null;
            }

            return 'text/plain';
        }

        return null;
    }

    private function looksLikeSvg(string $contents): bool
    {
        $start = ltrim(substr($contents, 0, 1024));

        if (str_starts_with($start, "\xEF\xBB\xBF")) {
            $start = ltrim(substr($start, 3));
        }

        return (bool) preg_match('/<(?:svg:)?svg\b/i', $start);
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
