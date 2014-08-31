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

use Radium\Database\Model;
use Radium\Database\Model\SecurePassword;

/**
 * User model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class User extends Model
{
    use SecurePassword;

    // Things to do before certain things
    protected static $_before = [
        'create' => ['preparePassword', 'createLoginHash', 'setName'],
    ];

    // Things to do after certain things
    protected static $_after = [
        'construct' => ['decodeOptions']
    ];

    // Belongs-to relationships
    protected static $_belongsTo = ['group'];

    // Has many relationships
    protected static $_hasMany = [
        'ticket_updates',
        'assigned_tickets' => ['foreignKey' => 'asssigned_to_id']
    ];

    // Users group and role ermissions
    protected $permissions = array(
        'project' => array(),
        'role' => array()
    );

    /**
     * Returns the URI for the users profile.
     *
     * @return string
     */
    public function href()
    {
        return "/users/{$this->id}";
    }

    /**
     * Returns an array containing an array of the project and role
     * the user belongs to.
     *
     * @return array
     */
    public function projects()
    {
        $projects = [];

        // Loop over the relations and add the project and role to the array
        UserRole::select()->where('user_id', $this->id)->fetchAll();
        foreach ($roles as $relation) {
            $projects[] = [$relation->project, $relation->role];
        }

        return $projects;
    }

    /**
     * Check if the user can perform the requested action.
     *
     * @param integer $proejct_id
     * @param string $action
     *
     * @return bool
     */
    public function permission($project_id, $action)
    {
        // Check if user is admin and return true
        // as admins have the right to do anything.
        if ($this->group()->is_admin) {
            return true;
        }

        // Check if the projects permissions has been fetched
        // if not, fetch them.
        if (!isset($this->permissions['project'][$project_id])) {
            $this->permissions['project'][$project_id] = Permission::getPermissions($project_id, $this->group_id);
        }

        // Check if the user has a role for the project and
        // fetch the permissions if not already done so...
        $role_id = $this->getProjectRole($project_id);
        if ($role_id and !isset($this->permissions['role'][$project_id])) {
            $this->permissions['role'][$project_id] = Permission::getPermissions($project_id, $role_id, 'role');
        } elseif (!isset($this->permissions['role'][$project_id])) {
            $this->permissions['role'][$project_id] = array();
        }

        $perms = array_merge($this->permissions['project'][$project_id], $this->permissions['role'][$project_id]);

        if (!isset($perms[$action])) {
            return false;
        }

        return $perms[$action]->value;
    }

    /**
     * Fetches the users project role.
     *
     * @param integer $project_id
     *
     * @return integer
     */
    public function getProjectRole($project_id)
    {
        $role = UserRole::select()
            ->where('project_id = ?', $project_id)
            ->_and('user_id = ?', $this->id)
            ->exec();

        if ($role->rowCount() > 0) {
            return $role->fetch()->project_role_id;
        } else {
            return 0;
        }
    }

    /**
     * Checks if the user has activated their account.
     *
     * @return bool
     */
    public function isActivated()
    {
        return !isset($this->options['activationKey']);
    }

    protected static $_validates = [
        'username' => ['required', 'unique', 'minLength' => 25],
        'name'     => ['required'],
        'password' => ['required'],
        'email'    => ['required', 'unique']
    ];

    public function generateActivationKey()
    {
        $this->options['activationKey'] = sha1(
            (microtime() + rand(0, 1000)) . $this->id . time()
        );
    }

    /**
     * Generates the users API key.
     */
    public function generateApiKey()
    {
        $this->api_key = sha1(
            (microtime() + rand(0, 1000)) . $this->id . (time() + rand(0, 1000))
        );
    }

    /**
     * Returns an array of the users data.
     *
     * @param array $fields Fields to return
     *
     * @return array
     */
    public function __toArray($fields = null)
    {
        $data = parent::__toArray($fields);
        unset($data['password'], $data['email'], $data['login_hash']);
        return $data;
    }

    /**
     * Moves ticket and timeline data to the anonymous user before deleting the user.
     */
    public function delete() {
        $anon_id = Setting::find('setting', 'anonymous_user_id')->value;

        // Update attachments, tickets, ticket updates and timeline events
        $tables = array('attachments', 'tickets', 'ticket_history', 'timeline');
        foreach ($tables as $table) {
            static::db()->update($table)->set(array('user_id' => $anon_id))->where('user_id', $this->id)->exec();
        }

        // Update assigned tickets
        static::db()->update('tickets')->set(array('assigned_to_id' => 0))->where('assigned_to_id', $this->id)->exec();

        // Delete subscriptions
        static::db()->delete()->from('subscriptions')->where('user_id', $this->id)->exec();

        // Delete user project roles
        static::db()->delete()->from('user_roles')->where('user_id', $this->id)->exec();

        // Delete user
        parent::delete();
    }

    //--------------------------------------------------------------------------
    // Before and after filters

    protected function setLoginHash()
    {
        $this->login_hash = sha1(time() . $this->username . rand(0, 1000));
    }

    protected function setName()
    {
        if (empty($this->name) || !isset($this->name)) {
            $this->name = $this->username;
        }
    }

    protected function decodeOptions()
    {
        $this->options = json_decode($this->options, true);
    }

    //--------------------------------------------------------------------------
    // Static methods

    public static function anonymousUser()
    {
        return new static([
            'id'       => Setting::find('anonymous_user_id')->value,
            'username' => "Guest",
            'group_id' => 3
        ]);
    }

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public static function selectOptions()
    {
        $options = [];

        // Get all users and make a Form::select() friendly array
        foreach (static::all() as $user) {
            $options[] = ['label' => $user->name, 'value' => $user->id];
        }

        return $options;
    }
}
