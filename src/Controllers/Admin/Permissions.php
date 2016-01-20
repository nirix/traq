<?php

namespace Traq\Controllers\Admin;

class Permissions extends AppController
{
    public function groupsAction()
    {
        $defaultPermissionsQuery = queryBuilder()->select('p.*', 'p.id AS permission_id')->from(PREFIX . 'permissions', 'p')
            ->where('type = ?')
            ->andWhere('type_id = ?')
            ->andWhere('project_id = ?')
            ->setParameter(0, 'usergroup')
            ->setParameter(1, 0)
            ->setParameter(2, 0)
            ->execute();

        $defaults = $defaultPermissionsQuery->fetch();
        $defaults = json_decode($defaults['permissions'], true);
        $permissions = [];

        $groupsQuery = queryBuilder()->select('g.*', 'p.permissions', 'p.type_id', 'p.id AS permission_id')
            ->from(PREFIX . 'usergroups', 'g')
            ->leftJoin('g', PREFIX . 'permissions', 'p', 'p.type = "usergroup" AND p.type_id = g.id')
            ->execute();

        foreach ($groupsQuery->fetchAll() as $group) {
            $group['permissions'] = json_decode($group['permissions'], true);
            $permissions[$group['id']] = $group;
        }

        return $this->render('admin/permissions/list.phtml', [
            'type'        => 'groups',
            'defaults'    => $defaults,
            'permissions' => $permissions
        ]);
    }

    public function rolesAction()
    {
        return $this->render('admin/permissions/list.phtml', [
            'type' => 'roles',
            'permisisons' => []
        ]);
    }
}
