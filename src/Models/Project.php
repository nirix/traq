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

namespace Traq\Models;

use Avalon\Database\Model;

/**
 * Project model.
 *
 * @author Jack P.
 */
class Project extends Model
{
    protected static $_validates = array(
        'name'          => array('unique', 'required'),
        'slug'          => array('unique', 'required'),
        'enable_wiki'   => array('required'),
        'display_order' => array('required', 'numeric'),
        'default_ticket_type_id' => array('required'),
        'default_ticket_sorting' => array('required')
    );

    // Has many relationships
    protected static $_hasMany = array(
        'roles' => ['model' => 'ProjectRole'],

        'tickets', 'milestones', 'components', 'subscriptions', 'permissions',
        'wikiPages', 'userRoles', 'customFields',
    );

    // Filters
    protected static $_filters_before = array(
        'create' => array('_before_create'),
        'save'   => array('_before_save')
    );

    /**
     * @var array
     */
    protected static $_dataTypes = [
        'enable_wiki' => "boolean"
    ];

    /**
     * Returns the URI for the project.
     *
     * @param mixed $uri Extra URI segments to add after the project URI.
     */
    public function href($uri = null)
    {
        return $this->slug . ($uri !== null ? '/' . implode('/', func_get_args()) : '');
    }

    /**
     * Returns an array formatted for the Form::select() options.
     *
     * @return array
     */
    public static function selectOptions()
    {
        $options = [];

        // Get all the rows and make a Form::select() friendly array
        foreach (static::all() as $row) {
            $options[] = ['label' => $row->name, 'value' => $row->id];
        }

        return $options;
    }

    /**
     * Returns an array of milestones formatted for the Form::select() method.
     *
     * @return array
     */
    public function milestoneSelectOptions($status = null, $sort = 'ASC')
    {
        $milestones = $this->milestones()->orderBy('display_order', $sort);

        // Check if we're fetching uncompleted milestones
        if ($status == 'open') {
            $milestones = $milestones->where('status = ?', 1);
        }
        // Or if we're fetching completed milestones
        elseif ($status == 'closed') {
            $milestones = $milestones->where('status = ?', 2);
        }
        // or even cancelled milestones
        elseif ($status == 'cancelled') {
            $milestones = $milestones->where('status = ?', 0);
        }

        $options = array();
        foreach ($milestones->fetchAll() as $milestone) {
            $options[] = ['label' => $milestone->name, 'value' => $milestone->id];
        }

        return $options;
    }

    /**
     * Returns an array of components belonging to the project formatted
     * for the Form::select method.
     */
    public function componentSelectOptions()
    {
        return Component::selectOptions($this->id);
    }

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public function memberSelectOptions()
    {
        $options = [];
        foreach (UserRole::projectMembers($this->id) as $user) {
            $options[] = ['label' => $user->name, 'value' => $user->id];
        }
        return $options;
    }

    /**
     * Returns an array of members belonging to a role that tickets can
     * be assigned to, formatted for the Form::select() helper.
     *
     * @return array
     */
    public function assignable_member_select_options()
    {
        $options = array();
        foreach ($this->user_roles->exec()->fetch_all() as $relation) {
            if ($relation->role->assignable) {
                $options[] = array('label' => $relation->user->name, 'value' => $relation->user->id);
            }
        }
        return $options;
    }

    /**
     * Converts the slug to be URI safe.
     */
    protected function _create_slug()
    {
        $this->slug = create_slug($this->slug);
    }

    /**
     * Things required before creating the table row.
     */
    protected function _before_create()
    {
        $this->_data['private_key'] = random_hash();
        $this->_create_slug();
    }

    /**
     * Do required stuff before saving.
     */
    protected function _before_save()
    {
        $this->_create_slug();
    }

    /**
     * Things to do before deleting the project...
     */
    public function delete()
    {
        $relations = [];

        foreach (static::$_hasMany as $index => $relation) {
            if (is_numeric($index)) {
                $relations = array_merge($relations, $this->{$relation}()->fetchAll());
            } else {
                $relations = array_merge($relations, $this->{$index}()->fetchAll());
            }
        }

        if (parent::delete()) {
            foreach ($relations as $relation) {
                $relation->delete();
            }
        }
    }

    /**
     * Returns an array of the project data.
     *
     * @param array $fields Fields to return
     *
     * @return array
     */
    public function __toArray($fields = null)
    {
        $data = parent::__toArray($fields);
        unset($data['private_key'], $data['next_tid']);
        return $data;
    }
}
