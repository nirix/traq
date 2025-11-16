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
use Avalon\Http\Middleware\MiddlewareInterface;
use Avalon\Http\Request;
use Avalon\Http\Response;
use Avalon\Http\Router;
use Avalon\Output\View;
use Traq\Models\Project;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class ProjectMiddleware implements MiddlewareInterface
{
    public function run(callable $next): Response
    {
        // No project, move along
        if (!isset(Router::$attributes['project_slug'])) {
            return $next();
        }

        if ($project = Project::find('slug', Router::$attributes['project_slug'])) {
            if (!Request::attribute('current_user')->permission($project->id, 'view')) {
                return new Response(View::render('errors/no_permission.phtml'), Response::HTTP_FORBIDDEN);
            }

            Request::set('project', $project);
        } else {
            return new Response(View::render('error/404.phtml'), Response::HTTP_NOT_FOUND);
        }

        return $next();
    }
}
