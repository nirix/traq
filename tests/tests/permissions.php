<?php

use Traq\Permissions;

$testSuite->createGroup('Permissions API', function ($g) {
    $g->test('Get defaults', function ($t) {
        $t->assertArray(Permissions::getDefaults());
        $t->assertArray(Permissions::getDefaults(true));
    });

    $g->test('Get permissions', function ($t) {
        $t->assertArray(Permissions::getPermissions());
        $t->assertArray(Permissions::getPermissions(true));
    });

    $g->test('Add permission', function ($t) {
        Permissions::add('test_add_permission', true, 'test');

        $permissions = Permissions::getPermissions();
        $permissionsWithCategories = Permissions::getPermissions(true);

        $t->assertTrue(isset($permissions['test_add_permission']));
        $t->assertTrue(isset($permissionsWithCategories['test']['test_add_permission']));
    });
});
