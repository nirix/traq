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

function createMilestone($projectId)
{
    $milestone = new Milestone([
        'name' => 'milestone-' . sha1(microtime()) . '-name',
        'slug' => 'milestone-' . sha1(microtime()) . '-slug',
        'project_id' => $projectId
    ]);
    $milestone->save();

    return $milestone;
}
