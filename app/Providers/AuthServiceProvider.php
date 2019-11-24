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

namespace Traq\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Traq\Milestone;
use Traq\Policies\MilestonePolicy;
use Traq\Policies\ProjectPolicy;
use Traq\Policies\TicketPolicy;
use Traq\Policies\UserGroupPolicy;
use Traq\Project;
use Traq\Permissions;
use Traq\Policies\WikiPolicy;
use Traq\Ticket;
use Traq\User;
use Traq\UserGroup;
use Traq\WikiPage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        UserGroup::class => UserGroupPolicy::class,
        Project::class => ProjectPolicy::class,
        Milestone::class => MilestonePolicy::class,
        Ticket::class => TicketPolicy::class,
        WikiPage::class => WikiPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admincp', function (User $user) {
            return $user->group->hasOneOfPermissions([
                Permissions::PERMISSION_ADMIN,
                Permissions::PERMISSION_PROJECT_CREATE,
                Permissions::PERMISSION_PROJECT_UPDATE,
                Permissions::PERMISSION_PROJECT_DELETE,
                Permissions::PERMISSION_USER_CREATE,
                Permissions::PERMISSION_USER_UPDATE,
                Permissions::PERMISSION_USER_DELETE,
                Permissions::PERMISSION_USER_GROUP_CREATE,
                Permissions::PERMISSION_USER_GROUP_UPDATE,
                Permissions::PERMISSION_USER_GROUP_DELETE,
            ]);
        });
    }
}
