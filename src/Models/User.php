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

use Avalon\Database\Model\SecurePassword;
use Traq\Models\Permission;

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

    protected static $_validations = [
        'username' => ['required', 'unique', 'minLength' => 3],
        'password' => ['required', 'minLength' => 6, 'confirm'],
        'email' => ['required', 'unique', 'email']
    ];

    protected static $_belongsTo = [
        'group'
    ];

    protected static $_before = [
        'create' => ['preparePassword', 'beforeCreate']
    ];

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->group()->isAdmin();
    }

    /**
     * Set defaults before creating user.
     */
    protected function beforeCreate()
    {
        $this->name = $this->name ?: $this->username;
        $this->session_hash = sha1($this->username . uniqid() . microtime() . rand(0, 99999));
    }

    // ------------------------------------------------------------------------
    // Overwritten functions

    /**
     * @var string $password
     *
     * @return boolean
     */
    public function authenticate($password)
    {
        if ($this->password_ver == 'sha1') {
            return sha1($password) == $this->password;
        }

        return parent::authenticate($password);
    }

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
