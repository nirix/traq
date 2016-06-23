<?php

use Traq\Models\Project;
use Traq\Models\Milestone;

function createProject()
{
    $project = new Project([
        'name' => 'project-' . sha1(microtime()) . '-name',
        'slug' => 'project-' . sha1(microtime()) . '-slug'
    ]);
    $project->save();

    return $project;
}

function createMilestone($project = null)
{
    if (!$project) {
        $project = createProject();
    }

    $milestone = new Milestone([
        'name' => 'milestone-' . sha1(microtime()) . '-name',
        'slug' => 'milestone-' . sha1(microtime()) . '-slug',
        'project_id' => $project['id']
    ]);
    $milestone->save();

    return $milestone;
}
