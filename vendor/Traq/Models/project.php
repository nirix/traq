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

namespace traq\models;

use avalon\database\Model;

/**
 * Project model.
 *
 * @package Traq
 * @subpackage Models
 * @since 3.0
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Project extends Model
{
    protected static $_name = 'projects';
    protected static $_properties = array(
        'id',
        'name',
        'codename',
        'slug',
        'info',
        'next_tid',
        'enable_wiki',
        'default_ticket_type_id',
        'default_ticket_sorting',
        'displayorder',
        'private_key'
    );

    protected static $_escape = array(
        'name',
        'codename'
    );

    // Has-many relationships with other models
    protected static $_has_many = array(
        'tickets', 'milestones', 'components', 'subscriptions', 'permissions',
        'wiki_pages'   => array('model' => 'WikiPage'),
        'roles'        => array('model' => 'ProjectRole'),
        'user_roles'   => array('model' => 'UserRole'),
        'repositories' => array('model' => 'Repository'),
        'custom_fields' => array('model' => 'CustomField')
    );

    // Filters
    protected static $_filters_before = array(
        'create' => array('_before_create'),
        'save' => array('_before_save')
    );

    /**
     * Returns the URI for the project.
     *
     * @param mixed $uri Extra URI segments to add after the project URI.
     */
    public function href($uri = null)
    {
        return $this->_data['slug'] . ($uri !== null ? '/' . implode('/', func_get_args()) : '');
    }

    /**
     * Returns an array formatted for the Form::select() options.
     *
     * @return array
     */
    public static function select_options()
    {
        $options = array();

        // Get all the rows and make a Form::select() friendly array
        foreach (static::fetch_all() as $row) {
            $options[] = array('label' => $row->name, 'value' => $row->id);
        }

        return $options;
    }

    /**
     * Returns an array of milestones formatted for the Form::select() method.
     *
     * @return array
     */
    public function milestone_select_options($status = null, $sort = 'ASC')
    {
        $milestones = Milestone::select()->where('project_id', $this->id)->order_by('displayorder', $sort);

        // Check if we're fetching uncompleted milestones
        if ($status == 'open') {
            $milestones = $milestones->where('status', 1);
        }
        // Or if we're fetching completed milestones
        elseif ($status == 'closed') {
            $milestones = $milestones->where('status', 2);
        }
        // or even cancelled milestones
        elseif ($status == 'cancelled') {
            $milestones = $milestones->where('status', 0);
        }

        $options = array();
        foreach ($milestones->exec()->fetch_all() as $milestone) {
            $options[] = array('label' => $milestone->name, 'value' => $milestone->id);
        }
        return $options;
    }

    /**
     * Returns an array of components belonging to the project formatted
     * for the Form::select method.
     */
    public function component_select_options()
    {
        return Component::select_options($this->id);
    }

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public function member_select_options()
    {
        $options = array();
        foreach (UserRole::project_members($this->id) as $user) {
            $options[] = array('label' => $user->name, 'value' => $user->id);
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
     * Checks if the model data is valid.
     *
     * @return bool
     */
    public function is_valid()
    {
        $errors = array();

        // Check if the name is empty
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        // Check if the slug is empty
        if (empty($this->_data['slug'])) {
            $errors['slug'] = l('errors.slug_blank');
        }

        // Make sure the slug isnt in use
        $project_slug = Project::select('id')->where('id', ($this->_is_new() ? 0 : $this->id), '!=')->where('slug', $this->_data['slug']);
        if ($project_slug->exec()->row_count()) {
            $errors['slug'] = l('errors.slug_in_use');
        }

        $this->errors = $errors;
        return !count($errors) > 0;
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
        if (parent::delete()) {
            // Delete tickets
            foreach ($this->tickets->exec()->fetch_all() as $ticket) {
                $ticket->delete();
            }

            // Delete milestones
            foreach ($this->milestones->exec()->fetch_all() as $milestone) {
                $milestone->delete();
            }

            // Delete timeline
            foreach (Timeline::select('id')->where('project_id', $this->_data['id'])->exec()->fetch_all() as $timeline) {
                $timeline->delete();
            }

            // Delete repositories
            /*foreach ($this->repositories->exec()->fetch_all() as $repo) {
                $repo->delete();
            }*/

            // Delete components
            foreach ($this->components->exec()->fetch_all() as $component) {
                $component->delete();
            }

            // Delete wiki pages
            foreach ($this->wiki_pages->exec()->fetch_all() as $wiki) {
                $wiki->delete();
            }

            // Delete subscriptions
            foreach ($this->subscriptions->exec()->fetch_all() as $sub) {
                $sub->delete();
            }

            // Delete roles
            foreach ($this->roles->exec()->fetch_all() as $role) {
                $role->delete();
            }

            // Delete members
            foreach ($this->user_roles->exec()->fetch_all() as $member) {
                $member->delete();
            }

            // Delete permissions
            foreach ($this->permissions->exec()->fetch_all() as $permission) {
                $permission->delete();
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
