<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Traq\Permissions;

class PermissionsTest extends TestCase
{
    public function testGetDefaults()
    {
        $this->assertArrayHasKey('project_settings', Permissions::getDefaults());
        $this->assertArrayHasKey('projects', Permissions::getDefaults(true));
    }

    public function testGetPermissions()
    {
        $this->assertArrayHasKey('project_settings', Permissions::getDefaults());
        $this->assertArrayHasKey('projects', Permissions::getDefaults(true));
    }

    public function testAddPermission()
    {
        Permissions::add('test_add_permission', true, 'test');

        $permissions = Permissions::getPermissions();
        $permissionsWithCategories = Permissions::getPermissions(true);

        $this->assertTrue(isset($permissions['test_add_permission']));
        $this->assertTrue(isset($permissionsWithCategories['test']['test_add_permission']));
    }

    /**
     * @expectedException \Exception
     */
    public function testPermissionExists()
    {
        Permissions::add('test_add_permission', true, 'test');
        Permissions::add('test_add_permission', true, 'test');
    }
}
