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
 * User model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class User extends Model
{
    protected static $_name = 'users';
    protected static $_properties = array(
        'id',
        'username',
        'password',
        'password_ver',
        'name',
        'email',
        'group_id',
        'locale',
        'options',
        'login_hash',
        'api_key',
        'created_at'
    );

    protected static $_escape = array(
        'username',
        'name'
    );

    // Things the user belongs to
    protected static $_belongs_to = array('group');

    // Things the user has many of
    protected static $_has_many = array(
        'tickets',

        'ticket_updates' => array('model' => 'TicketHistory'),
        'assigned_tickets' => array('model' => 'ticket', 'foreign_key' => 'assigned_to_id')
    );

    // Things to do before certain things
    protected static $_filters_before = array(
        'create' => array('_before_create')
    );

    // Things to do after certain things
    protected static $_filters_after = array(
        'construct' => array('_after_construct')
    );

    // Users group and role ermissions
    protected $permissions = array(
        'project' => array(),
        'role' => array()
    );

    private $_options;

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
        $projects = array();

        // Loop over the relations and add the project and role to the array
        foreach (UserRole::select()->where('user_id', $this->_data['id'])->exec()->fetch_all() as $relation) {
            $projects[] = array($relation->project, $relation->role);
        }


        return $projects;
    }

    /**
     * Sets, or gets, the option.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return string
     */
    public function option($option, $value = '!donothing')
    {
        if ($value != '!donothing') {
            $this->_options[$option] = $value;
            $this->set('options', json_encode($this->_options));
        }

        return (isset($this->_options[$option])) ? $this->_options[$option] : false;
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
        if ($this->group->is_admin) {
            return true;
        }

        // Check if the projects permissions has been fetched
        // if not, fetch them.
        if (!isset($this->permissions['project'][$project_id])) {
            $this->permissions['project'][$project_id] = Permission::get_permissions($project_id, $this->_data['group_id']);
        }

        // Check if the user has a role for the project and
        // fetch the permissions if not already done so...
        $role_id = $this->get_project_role($project_id);
        if ($role_id and !isset($this->permissions['role'][$project_id])) {
            $this->permissions['role'][$project_id] = Permission::get_permissions($project_id, $role_id, 'role');
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
    public function get_project_role($project_id)
    {
        if ($role = UserRole::select()->where('project_id', $project_id)->where('user_id', $this->_data['id'])->exec()
        and $role->row_count() > 0) {
            return $role->fetch()->project_role_id;
        } else {
            return 0;
        }
    }

    /**
     * Checks the given password against the users password.
     *
     * @param string $password
     *
     * @return bool
     */
    public function verify_password($password)
    {
        switch($this->_data['password_ver']) {
            // Passwords from Traq 0.1 to 2.3
            case 'sha1':
                return sha1($password) == $this->_data['password'];
                break;

            // Passwords from Traq 3+
            case 'crypt':
                return crypt($password, $this->_data['password']) == $this->_data['password'];
                break;
        }
    }

    /**
     * Sets the users password.
     *
     * @param string $new_password
     */
    public function set_password($new_password)
    {
        $this->password = $new_password;
        $this->password_ver = 'crypt';
        $this->prepare_password();
    }

    /**
     * Checks if the user has activated their account.
     *
     * @return bool
     */
    public function is_activated()
    {
        return !$this->option('validation_key');
    }

    /**
     * Handles all the required stuff before creating
     * the user, such as hashing the password.
     */
    protected function _before_create()
    {
        $this->prepare_password();
        $this->_data['login_hash'] = sha1(time() . $this->_data['username'] . rand(0, 1000));

        if (!isset($this->_data['name'])) {
            $this->_data['name'] = $this->_data['username'];
        }
    }

    /**
     * Handles all the required after the model
     * construction.
     */
    protected function _after_construct()
    {
        if (isset($this->_data) and isset($this->_data['options'])) {
            $this->_options = json_decode($this->_data['options'], true);
        }
    }

    /**
     * Hashes the users password.
     */
    public function prepare_password()
    {
        $this->_data['password'] = crypt($this->_data['password'], '$2a$10$' . sha1(microtime() . $this->_data['username'] . $this->_data['email']) . '$');
    }

    /**
     * Checks if the users data is valid or not.
     *
     * @return bool
     */
    public function is_valid()
    {
        // Check if the username is set
        if (empty($this->_data['username'])) {
            $this->errors['username'] = l('errors.users.username_blank');
        }

        // Check username length
        if (isset($this->_data['username'][25])) {
            $this->errors['username'] = l('errors.users.username_too_long');
        }

        // Check if the username is taken
        if ($this->_is_new() and static::find('username', $this->_data['username'])) {
            $this->errors['username'] = l('errors.users.username_in_use');
        }

        // Make sure the users name is set
        if (empty($this->_data['name'])) {
            $this->errors['name'] = l('errors.users.name_blank');
        }

        // Check if the password is set
        if (empty($this->_data['password'])) {
            $this->errors['password'] = l('errors.users.password_blank');
        }

        // check if user changed password
        if (isset($this->_data['old_password']) && (isset($this->_data['new_password']) || isset($this->_data['confirm_password']))) {
            if (empty($this->_data['old_password'])) {
                $this->errors['old_password'] = l('errors.users.password_blank');
            } elseif (empty($this->_data['new_password'])) {
                $this->errors['new_password'] = l('errors.users.new_password_blank');
            } elseif (empty($this->_data['confirm_password'])) {
                $this->errors['cofirm_password'] = l('errors.users.confirm_password_blank');
            } elseif ($this->_data['new_password'] !== $this->_data['confirm_password']) {
                $this->errors['new_password'] = l('errors.users.invalid_confirm_password');
            } elseif(!$this->verify_password($this->_data['old_password'])) {
                $this->errors['old_password'] = l('errors.users.invalid_password');
            } elseif($this->_data['new_password'] === $this->_data['old_password']) {
                $this->errors['old_password'] = l('errors.users.password_same');
            } else {
                // password should be valid at this point
                unset($this->_data['old_password'], $this->_data['new_password'], $this->_data['confirm_password']);
            }
        }


        // Check if the email is set
        if (empty($this->_data['email'])) {
            $this->errors['email'] = l('errors.users.email_invalid');
        }

        // Require unique email
        if ($this->_is_new() and static::find('email', $this->_data['email'])) {
            $this->errors['email'] = l('errors.users.email_in_use');
        }

        // Check if we're valid or not...
        if (count($this->errors) > 0) {
            $this->errors = $this->errors;
        }

        return !count($this->errors) > 0;
    }

    /**
     * Generates the users API key.
     */
    public function generate_api_key()
    {
        $this->api_key = sha1(microtime() . rand(0, 1000) . time() + rand(0, 1000) . $this->email . $this->id . $this->created_at);
    }

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public static function select_options()
    {
        $options = array();

        // Get all users and make a Form::select() friendly array
        foreach (static::fetch_all() as $user) {
            $options[] = array('label' => $user->name, 'value' => $user->id);
        }

        return $options;
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
    public function delete()
    {
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

        // Delete timeline events
        static::db()->delete()->from('timeline')->where('user_id', $this->id)->exec();

        // Delete user
        parent::delete();
    }
}
