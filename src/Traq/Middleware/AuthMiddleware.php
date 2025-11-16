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
use Avalon\Http\JsonResponse;
use Avalon\Http\Middleware\MiddlewareInterface;
use Avalon\Http\Request;
use Avalon\Http\Response;
use Avalon\Output\View;
use Traq\Models\Project;
use Traq\Models\User;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class AuthMiddleware implements MiddlewareInterface
{
    protected User $user;
    protected ?Project $project = null;

    public function __construct(protected array $permissions = [], protected bool $requireAll = true, protected bool $admin = false)
    {
        $this->user = Request::attribute('current_user');
        $this->project = Request::attribute('project');
    }

    public function run(callable $next): Response
    {
        if (!$this->user) {
            return $this->noPermissionResponse();
        }

        // You shall not pass!... if you're not an admin
        if ($this->admin && !$this->user->group->is_admin) {
            return $this->noPermissionResponse();
        }

        // Are all the permissions required? if yes, then make sure the user has all of them
        if ($this->requireAll && !$this->userCanAll()) {
            return $this->noPermissionResponse();
        } elseif (!$this->requireAll && !$this->userCanAny()) {
            // If not all, then make sure the user has at least one of the permissions
            return $this->noPermissionResponse();
        }

        return $next();
    }

    private function userCanAll(): bool
    {
        foreach ($this->permissions as $permission) {
            if (!$this->user->permission($this->project->id, $permission)) {
                return false;
            }
        }

        return true;
    }

    private function userCanAny(): bool
    {
        foreach ($this->permissions as $permission) {
            if ($this->user->permission($this->project->id, $permission)) {
                return true;
            }
        }

        return false;
    }

    private function noPermissionResponse(): Response
    {
        // If the request accepts application/json, return a JSON response
        if (Request::header('Accept') === 'application/json') {
            return new JsonResponse([
                'error' => l('errors.no_permission.message'),
            ], Response::HTTP_FORBIDDEN);
        }

        return new Response(View::render('error/no_permission.phtml'), Response::HTTP_FORBIDDEN);
    }
}
