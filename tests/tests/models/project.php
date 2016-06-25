<?php

use Traq\Models\Project;

$testSuite->createGroup('Models / Project', function ($g) {
    $project = new Project([
        'name' => 'Project Model Test',
        'slug' => 'project-model-test'
    ]);

    $g->test('Create', function ($t) use ($project) {
        $t->assertTrue($project->save());
    });

    $g->test('Update', function ($t) use ($project) {
        $project['name'] = 'Project Model Test - Updated';

        $t->assertTrue($project->save());
    });

    $g->test('Slug in use', function ($t) {
        $project = new Project([
            'slug' => 'project-model-test'
        ]);

        $t->assertFalse($project->save());
        $t->assertEquals('Slug is already in use', $project->getError('slug')[0]);
    });

    $g->test('Get select options', function ($t) {
        $options = Project::selectOptions();

        $t->assertArray($options);
    });

    $g->test('Delete', function ($t) use ($project) {
        $project->delete();
        $t->assertFalse(Project::find('slug', 'project-model-test'));
    });
});
