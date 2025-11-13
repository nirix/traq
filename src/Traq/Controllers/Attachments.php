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

use Avalon\Http\Response;
use Avalon\Http\Router;
use Traq\Controllers\AppController;
use Traq\Models\Attachment;

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
    // Content type categories that can be displayed inline
    private const INLINE_CONTENT_TYPES = ['text', 'image'];

    // Maximum file size to serve (50MB default, prevents memory exhaustion)
    private const MAX_SERVE_SIZE = 52428800;

    // Before filters for permission checking
    public $before = [
        'view' => ['_check_permission'],
        'delete' => ['_check_permission']
    ];

    private ?Attachment $attachment = null;

    /**
     * Serve an attachment file
     *
     * @return Response
     */
    public function view(): Response
    {
        // Validate attachment data integrity
        if (!$this->validateAttachment($this->attachment)) {
            return $this->createErrorResponse('Invalid or corrupted attachment', 422);
        }

        // Security: Prevent memory exhaustion attacks
        if ($this->attachment->size > self::MAX_SERVE_SIZE) {
            return $this->createErrorResponse('File too large to serve', 413);
        }

        try {
            $decodedContent = base64_decode($this->attachment->contents, true);

            if ($decodedContent === false) {
                throw new \RuntimeException('Failed to decode attachment content');
            }

            $headers = $this->buildSecureHeaders($this->attachment);

            return new Response($decodedContent, 200, $headers);
        } catch (\Exception $e) {

            return $this->createErrorResponse('Failed to serve attachment', 500);
        }
    }

    /**
     * Delete an attachment
     *
     * @return Response
     */
    public function delete(): Response
    {
        try {
            $ticketHref = $this->attachment->ticket->href();

            if (!$this->attachment->delete()) {
                throw new \RuntimeException('Failed to delete attachment');
            }

            return $this->redirectTo($ticketHref);
        } catch (\Exception $e) {
            return $this->createErrorResponse('Failed to delete attachment', 500);
        }
    }

    /**
     * Permission check middleware
     *
     * Validates attachment exists and user has required permissions
     *
     * @param string $action Action name (view|delete)
     * @return bool
     */
    public function _check_permission(string $action): bool
    {
        $attachmentId = $this->extractAttachmentId();

        if ($attachmentId === null) {
            $this->show404();
            return false;
        }

        $this->attachment = Attachment::find($attachmentId);

        if (!$this->attachment) {
            $this->show404();
            return false;
        }

        // Validate attachment has required relationships
        if (!$this->attachment->ticket || !$this->attachment->ticket->project_id) {
            $this->show404();
            return false;
        }

        // Check user permissions
        $permissionKey = "{$action}_attachments";
        if (!$this->user->permission($this->attachment->ticket->project_id, $permissionKey)) {
            $this->show_no_permission();
            return false;
        }

        return true;
    }

    /**
     * Build secure HTTP headers for attachment response
     *
     * @param Attachment $attachment
     * @return array
     */
    private function buildSecureHeaders(Attachment $attachment): array
    {
        $contentType = $this->sanitizeContentType($attachment->type);
        $primaryType = $this->extractPrimaryContentType($contentType);

        $headers = [
            'Content-Type' => $contentType,
            'Content-Length' => (string) $attachment->size,
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline';",
        ];

        // Determine content disposition based on content type
        if (in_array($primaryType, self::INLINE_CONTENT_TYPES, true)) {
            // Override text files to plain text for security
            if ($primaryType === 'text') {
                $headers['Content-Type'] = 'text/plain; charset=utf-8';
            }
            $headers['Content-Disposition'] = sprintf(
                'inline; filename="%s"',
                $this->sanitizeFilename($attachment->name)
            );
        } else {
            // Force download for non-safe content types
            $headers['Content-Disposition'] = sprintf(
                'attachment; filename="%s"',
                $this->sanitizeFilename($attachment->name)
            );
        }

        return $headers;
    }

    /**
     * Sanitize content type to prevent header injection
     *
     * @param string $contentType
     * @return string
     */
    private function sanitizeContentType(string $contentType): string
    {
        // Remove any newlines or control characters
        $sanitized = preg_replace('/[\r\n\x00-\x1F\x7F]/', '', $contentType);

        // Validate it looks like a mime type
        if (!preg_match('/^[\w\-\.]+\/[\w\-\.+]+$/i', $sanitized)) {
            return 'application/octet-stream';
        }

        return $sanitized;
    }

    /**
     * Sanitize filename to prevent header injection and XSS
     *
     * @param string $filename
     * @return string
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove path traversal attempts
        $filename = basename($filename);

        // Remove quotes and other dangerous characters
        $filename = str_replace(['"', "'", '\\', "\r", "\n", "\0"], '', $filename);

        // Ensure reasonable length
        if (strlen($filename) > 255) {
            $filename = substr($filename, 0, 255);
        }

        return $filename ?: 'attachment';
    }

    /**
     * Extract primary content type from full MIME type
     *
     * @param string $contentType
     * @return string
     */
    private function extractPrimaryContentType(string $contentType): string
    {
        $parts = explode('/', $contentType, 2);
        return strtolower($parts[0]);
    }

    /**
     * Validate attachment integrity
     *
     * @param Attachment|null $attachment
     * @return bool
     */
    private function validateAttachment(?Attachment $attachment): bool
    {
        if (!$attachment) {
            return false;
        }

        $name = $attachment->name;
        $type = $attachment->type;
        $contents = $attachment->contents;
        $size = $attachment->size;

        return !empty($name)
            && !empty($type)
            && !empty($contents)
            && is_numeric($size)
            && $size > 0;
    }

    /**
     * Extract attachment ID from router params safely
     *
     * @return int|null
     */
    private function extractAttachmentId(): ?int
    {
        $id = Router::$attributes['attachment_id'] ?? null;

        if ($id === null || !is_numeric($id) || $id < 1) {
            return null;
        }

        return (int) $id;
    }

    /**
     * Create a standardized error response
     *
     * @param string $message
     * @param int $statusCode
     * @return Response
     */
    private function createErrorResponse(string $message, int $statusCode): Response
    {
        $content = sprintf(
            '<!DOCTYPE html><html><head><title>Error</title></head><body><h1>%s</h1></body></html>',
            htmlspecialchars($message, ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );

        return new Response($content, $statusCode, [
            'Content-Type' => 'text/html; charset=utf-8'
        ]);
    }
}
