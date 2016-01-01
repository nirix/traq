<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
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

namespace Traq\Models;

use Avalon\Database\Model;

/**
 * Project model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Project extends Model
{
    protected static $_validations = array(
        'name'          => ['unique', 'required'],
        'slug'          => ['unique', 'required'],
        'enable_wiki'   => ['boolean'],
        'display_order' => ['numeric'],
        'default_ticket_type_id' => ['required'],
        'default_ticket_sorting' => ['required']
    );

    // Filters
    protected static $_filters_before = [
        'create' => ['_before_create'],
        'save'   => ['_before_save']
    ];

    /**
     * @var array
     */
    protected static $_dataTypes = [
        'enable_wiki' => "boolean"
    ];

    /**
     * @return array[]
     */
    public static function selectOptions()
    {
        $options = [];
        $projects = static::select('id', 'name')->execute()->fetchAll();

        foreach ($projects as $project) {
            $options[] = ['label' => $project['name'], 'value' => $project['id']];
        }

        return $options;
    }

    /**
     * @return array[]
     */
    public function milestoneSelectOptions($valueField = 'id')
    {
        $options = [];
        $milestones = Milestone::where('project_id = ?')->setParameter(0, $this->id)->orderBy('display_order', 'ASC');

        foreach ($milestones->execute()->fetchAll() as $milestone) {
            $options[] = ['label' => $milestone['name'], 'value' => $milestone['slug']];
        }

        return $options;
    }

    /**
     * @return array[]
     */
    public function memberSelectOptions()
    {
        $options = [];

        $query = queryBuilder()->select('ur.user_id', 'u.name AS user_name')
            ->from(PREFIX . 'user_roles', 'ur')
            ->leftJoin('ur', PREFIX . 'users', 'u', 'u.id = ur.user_id')
            ->where('project_id = ?')
            ->setParameter(0, $this->id);

        foreach ($query->execute()->fetchAll() as $row) {
            $options[] = ['label' => $row['user_name'], 'value' => $row['user_id']];
        }

        return $options;
    }

    /**
     * @return array[]
     */
    public function componentSelectOptions()
    {
        $options = [];
        $components = Component::where('project_id = ?')->setParameter(0, $this->id)->orderBy('name', 'ASC');

        foreach ($components->execute()->fetchAll() as $component) {
            $options[] = ['label' => $component['name'], 'value' => $component['id']];
        }

        return $options;
    }
}
