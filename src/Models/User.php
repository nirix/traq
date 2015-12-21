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

use DateTime;
use Avalon\Database\Model;
use Avalon\Database\Model\SecurePassword;
use Traq\Permissions;

/**
 * User model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class User extends Model
{
    protected $securePasswordField = 'password';
    use SecurePassword;

    protected static $_tableAlias = 'u';

    protected static $_validations = [
        'username' => ['unique', 'minLength' => 2, 'noWhitespace', 'alnum'],
        'email'    => ['unique', 'email'],
        'password' => ['minLength' => 5]
    ];

    protected static $_before = [
        'create' => ['prepareCreation', 'preparePassword']
    ];

    protected $permissions;

    /**
     * Authenticates the password with the users current password.
     *
     * @param string $password
     *
     * @return boolean
     */
    public function authenticate($password)
    {
        if ($this->password_ver == 'crypt') {
            return $this->password === crypt($password, $this->password);
        } else {

        }
    }

    /**
     * Check if the user is validated.
     *
     * @return boolean
     */
    public function isActivated()
    {
        return ! queryBuilder()->select('id')->from(PREFIX . 'user_activation_codes')
            ->where('user_id = ?')
            ->andWhere('type = ?')
            ->setParameter(0, $this->id)
            ->setParameter(1, 'email_validation')
            ->execute()
            ->rowCount();
    }

    /**
     * Check if the user can perform the requested action.
     *
     * @param integer $project_id
     * @param string  $action
     * @param boolean $fetchProjectRoles
     *
     * @return bool
     */
    public function hasPermission($projectId, $action, $fetchProjectRoles = false)
    {
        // Admins are godlike
        if ($this->is_admin) {
            return true;
        }

        if (!isset($this->permissions[$projectId])) {
            $this->permissions[$projectId] = null;
        }

        // No need to fetch permissions if we already have
        if ($this->permissions[$projectId] === null) {
            // Get group permissions
            $group = Permission::getPermissions($projectId, $this->group_id);

            // Get role permissions
            $role = [];
            if (!$fetchProjectRoles && isset($this->project_role_id) && $this->project_role_id) {
                $role = Permission::getPermissions($projectId, $this->project_role_id, 'role');
            } else {
                $roles = $this->fetchProjectRolesIds();
                if (isset($roles[$projectId])) {
                    $role = Permission::getPermissions($projectId, $roles[$projectId], 'role');
                }
            }

            // Merge group and role permissions
            $this->permissions[$projectId] = array_merge(Permissions::getPermissions(), array_merge($group, $role));
        }

        return isset($this->permissions[$projectId][$action])
                ? $this->permissions[$projectId][$action]
                : null;
    }

    protected function fetchProjectRolesIds()
    {
        $ids = [];
        $roles = queryBuilder()->select('project_id', 'project_role_id')->from(PREFIX . 'user_roles')
            ->where('user_id = ?')->setParameter(0, $this->id);

        foreach ($roles->execute()->fetchAll() as $row) {
            $ids[$row['project_id']] = $row['project_role_id'];
        }

        return $ids;
    }

    protected function prepareCreation()
    {
        if (!$this->name) {
            $this->name = $this->username;
        }

        $this->login_hash = sha1(microtime() . random_hash() . rand(0, 5000));
    }

    // ------------------------------------------------------------------------
    // Overwritten functions

    /**
     * Add password confirmation validation.
     */
    public function validate()
    {
        $parent = parent::validate();

        if (isset($this->password_confirmation) && $this->password_confirmation !== $this->password) {
            $this->addValidationError('password', 'confirm');
        }

        return $parent;
    }
}
