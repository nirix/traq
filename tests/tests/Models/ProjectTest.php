<?php

namespace Tests\Models;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Project;

class ProjectTest extends TestCase
{
    protected static $project;

    /**
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$project = new Project([
            'name' => 'Project Model Test',
            'slug' => 'project-model-test'
        ]);
    }

    public function testCreate()
    {
        $this->assertTrue(static::$project->save());
    }

    public function testUpdate()
    {
        static::$project['name'] = 'Project Model Test - Updated';

        $this->assertTrue(static::$project->save());
    }

    public function assertSlugInUse()
    {
        static::$project = new Project([
            'slug' => 'project-model-test'
        ]);

        $this->assertFalse(static::$project->save());
        $this->assertEquals('Slug is already in use', static::$project->getError('slug')[0]);
    }

    public function testGetSelectOptions()
    {
        $options = Project::selectOptions();

        $this->assertTrue(is_array($options));
    }

    public function testDelete()
    {
        static::$project->delete();
        $this->assertFalse(Project::find('slug', 'project-model-test'));
    }
}
