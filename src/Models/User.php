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

class User extends Model
{
    protected $securePasswordField = 'password';
    use SecurePassword;

    protected static $_belongsTo = [
        'group'
    ];

    public function isAdmin()
    {
        return $this->group()->isAdmin();
    }

    protected function getPermissions()
    {
        // SELECT
        //     d.permissions AS defaults,
        //     td.permissions AS type_defaults,
        //     pd.permissions AS project_defaults,
        //     t.permissions AS type_permissions
        // FROM
        //     t_permissions d
        // LEFT JOIN t_permissions td ON (td.project_id = 0 AND td.type = 'usergroup' AND td.type_id = 3)
        // LEFT JOIN t_permissions pd ON (pd.project_id = 1 AND pd.type = 'usergroup' AND pd.type_id = 0)
        // LEFT JOIN t_permissions t ON (t.project_id = 1 AND t.type = 'usergroup' AND t.type_id = 3)
        // WHERE
        //     d.project_id = 0
        // AND
        //     d.type = 'usergroup'
        // AND
        //     d.type_id = 0

        $query = Permission::select(
            'd.permissions AS defaults',
            'td.permissions AS type_defaults',
            'pd.permissions AS project_defaults',
            't.permissions AS type_permissions'
        );

        // $query->leftJoin('')
    }
}
