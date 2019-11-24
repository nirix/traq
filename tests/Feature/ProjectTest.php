<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Traq\Project;

class ProjectTest extends TestCase
{
    /**
     * @return void
     */
    public function testProjectListing()
    {
        $project = factory(Project::class)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSeeText($project->name);
    }

    public function testShowProject()
    {
        $project = factory(Project::class)->create();

        $response = $this->get(route('project.show', ['project' => $project]));

        $response->assertStatus(200);
        $response->assertSeeText($project->description);
    }
}
