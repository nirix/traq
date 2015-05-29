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

namespace Traq\Controllers;

use Traq\API;
use Traq\Helpers\Format;

/**
 * Roadmap controller.
 *
 * @author Jack P.
 * @package Traq\Controllers
 * @since 4.0.0
 */
class Roadmap extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->before('*', [$this, 'setTitle']);
    }

    /**
     * Roadmap index
     *
     * @param string $filter Which milestones to display.
     */
    public function indexAction($filter = 'active')
    {
        if ($filter !== 'active') {
            $this->title($this->translate($filter));
        }

        $milestones = $this->project->milestones()
            ->orderBy('display_order', 'ASC');

        // Filter by active, completed and cancelled milestones
        if ($filter == 'active') {
            $milestones = $milestones->where('status = ?', 1);
        } elseif ($filter == 'completed') {
            $milestones = $milestones->where('status = ?', 2);
        } elseif ($filter == 'cancelled') {
            $milestones = $milestones->where('status = ?', 0);
        }

        $milestones = $milestones->fetchAll();
        $this->set(compact('milestones'));

        return  $this->respondTo(function ($format) use ($milestones){
            if ($format == 'html') {
                return $this->render('roadmap/index.phtml');
            } elseif ($format == 'json') {
                return $this->jsonResponse($milestones);
            }
        });
    }

    /**
     * Milestone info page.
     *
     * @param string $milestone Milestone slug
     */
    public function showAction($slug)
    {
        $milestone = $this->project->milestones()
            ->where('slug = ?', $slug)->fetch();

        $this->title($milestone->name);

        return $this->respondTo(function ($format) use ($milestone) {
            if ($format == 'html') {
                return $this->render('roadmap/show.phtml', ['milestone' => $milestone]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($milestone->toArray());
            }
        });
    }

    public function setTitle()
    {
        $this->title($this->translate('roadmap'));
    }
}
