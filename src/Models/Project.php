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

namespace Traq\Models;

/**
 * Project model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Project extends Model
{
    protected static $_validations = [
        'name' => ['required'],
        'slug' => ['required', 'unique']
    ];

    protected static $_hasMany = [
        'milestones',
        'tickets',
        'wikiPages'
    ];

    protected static $_dataTypes = [
        'enable_wiki' => 'boolean'
    ];

    /**
     * @return array[]
     */
    public static function selectOptions()
    {
        $options = [];

        foreach (static::all() as $project) {
            $options[] = [
                'label' => $project['name'],
                'value' => $project['id']
            ];
        }

        return $options;
    }

    /**
     * @return array[]
     */
    public function milestoneSelectOptions($valueField = 'id', $status = null, $sort = 'ASC')
    {
        $options = [];
        $milestones = Milestone::where('project_id = ?')
            ->setParameter(0, $this->id)
            ->orderBy('display_order', $sort);

        if ($status !== null) {
            $milestones->andWhere('status = ?')->setParameter(1, $status);
        }

        foreach ($milestones->execute()->fetchAll() as $milestone) {
            $options[] = ['label' => $milestone['name'], 'value' => $milestone[$valueField]];
        }

        return $options;
    }

    /**
     * @return array[]
     */
    public function memberSelectOptions()
    {
        $options = [];

        $query = static::connection()->createQueryBuilder()->select('ur.user_id', 'u.name AS user_name')
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

    /**
     * Delete project.
     */
    public function delete()
    {
        foreach ($this->milestones()->fetchAll() as $milestone) {
            $milestone->delete();
        }

        foreach ($this->wikiPages()->fetchAll() as $page) {
            $page->delete();
        }

        return parent::delete();
    }
}
