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

namespace Traq\Models;

use Avalon\Database\Model;
use Avalon\Database\Model\SecurePassword;

/**
 * User model.
 *
 * @author Jack P.
 */
class User extends Model
{
    use SecurePassword;

    /**
     * Property to use for the secure password trait.
     *
     * @var string
     */
    protected $securePasswordField = 'password';

    /**
     * Validations
     *
     * @var array
     */
    protected static $_validates = [
        'username' => ['required', 'unique'],
        'name'     => ['required'],
        'password' => ['required'],
        'email'    => ['required', 'unique']
    ];

    /**
     * Before filters.
     *
     * @var array
     */
    protected static $_before = [
        'create' => ['preparePassword', 'createLoginHash', 'createName'],
    ];

    /**
     * Belongs-to relationships.
     *
     * @var array
     */
    protected static $_belongsTo = ['group'];

    /**
     * Has-many relationships.
     *
     * @var array
     */
    protected static $_hasMany = [
        'ticket_updates',
        'assigned_tickets' => ['foreignKey' => 'asssigned_to_id']
    ];

    /**
     * Cached permissions.
     *
     * @var array
     */
    protected $permissions;

    /**
     * Property data types.
     *
     * @var array
     */
    protected static $_dataTypes = [
        'options' => 'json_array'
    ];

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
        // With great power, comes great responsibility.
        if ($this->group()->is_admin) {
            return true;
        }

        // No need to fetch permissions if we already have
        if ($this->permissions === null) {
            // Get group permissions
            $group = Permission::getPermissions($project_id, $this->group()->id);

            // Get role permissions
            $role = [];
            if ($projectRoleId = $this->getProjectRole($project_id)) {
                $role = Permission::getPermissions($project_id, $projectRoleId, 'role');
            }

            // Merge group and role permissions
            $this->permissions = array_merge($group, $role);
        }

        return isset($this->permissions[$action])
                ? $this->permissions[$action]
                : null;
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
            ->andWhere('user_id = ?', $this->id)
            ->execute();

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

    /**
     * Generates the users activation key.
     */
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

    /**
     * Creates the users login hash.
     */
    protected function createLoginHash()
    {
        $this->login_hash = sha1(time() . $this->username . rand(0, 1000));
    }

    /**
     * Set the users username as their name if they didn't provide one.
     */
    protected function createName()
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

    /**
     * Returns the anonymous user.
     */
    public static function anonymousUser()
    {
        return new static([
            'id'       => Setting::get('anonymous_user_id')->value,
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
