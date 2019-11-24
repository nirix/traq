<?php
/*!
 * Traq
 *
 * Copyright (C) 2009-2019 Jack P.
 * Copyright (C) 2012-2019 Traq.io
 * https://github.com/nirix
 * https://traq.io
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq\Http\Controllers\Installer;

class PermissionController extends Controller
{
    private $requiredPermissions = [
        'storage/framework' => '775',
        'storage/logs' => '775',
        'bootstrap/cache' => '775',
    ];

    private $permissions = [];
    private $errors = false;

    public function index() {
        $permissions = $this->checkPermissions();

        return view('installer.permissions', [
            'step' => 'Filesystem Permissions',
            'permissions' => $permissions,
            'errors' => $this->errors,
        ]);
    }

    private function checkPermissions()
    {
        foreach ($this->requiredPermissions as $path => $permissions) {
            // If the file/directory doesn't exist, fail and continue.
            try {
                $permission = substr(sprintf('%o', fileperms(base_path($path))), -4);
            } catch (\ErrorException $e) {
                $this->errors = true;

                $this->permissions[] = [
                    'path' => $path,
                    'permission' => false,
                    'valid' => false,
                ];

                continue;
            }

            // Check existing file/directory.
            if (!($permission >= $permissions)) {
                $this->errors = true;

                $this->permissions[] = [
                    'path' => $path,
                    'permission' => $permission,
                    'valid' => false,
                ];
            } else {
                $this->permissions[] = [
                    'path' => $path,
                    'permission' => $permission,
                    'valid' => true,
                ];
            }
        }

        return $this->permissions;
    }
}
