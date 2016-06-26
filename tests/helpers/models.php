<?php

use Traq\Models\User;
use Traq\Models\Group;
use Traq\Models\Project;
use Traq\Models\Ticket;
use Traq\Models\Milestone;
use Traq\Models\WikiPage;
use Traq\Models\WikiRevision;

function createUser($password = null, $group = null)
{
    if (!$group) {
        $groupId = 3;
    } else {
        $groupId = $group['id'];
    }

    $user = new User([
        'name'     => 'user-' . substr(sha1(microtime()), 0, 5) . '-name',
        'username' => 'user-' . substr(sha1(microtime()), 0, 10) . '-username',
        'email'    => 'user-' . substr(sha1(microtime()), 0, 10) . '-email@example.com',
        'password' => $password ?: microtime(),
        'group_id' => $groupId
    ]);
    $user->save();

    return $user;
}

function createAdmin($password = null, $group = null)
{
    return createUser($password, Group::find(1));
}

function createGroup()
{
    $group = new Group([
        'name' => 'group-' . substr(sha1(microtime()), 0, 5) . '-name'
    ]);
    $group->save();

    return $group;
}

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

function createTicket($project = null, $milestone = null, $user = null)
{
    if (!$project) {
        $project = createProject();
    }

    if (!$milestone) {
        $milestone = createMilestone($project);
    }

    if (!$user) {
        $user = createUser();
    }

    $ticket = new Ticket([
        'ticket_id'    => $project['next_ticket_id'],
        'summary'      => 'ticket-' . sha1(microtime()) . '-summary',
        'body'         => 'ticket-' . sha1(microtime()) . '-body',
        'project_id'   => $project['id'],
        'user_id'      => $user['id'],
        'type_id'      => 1,
        'milestone_id' => $milestone['id']
    ]);
    $ticket->save();

    $project['next_ticket_id'] = $project['next_ticket_id'] + 1;
    $project->save();

    return $ticket;
}

function createWikiPage($project = null, $user = null)
{
    if (!$project) {
        $project = createProject();
    }

    if (!$user) {
        $user = createUser();
    }

    $prefix = 'wikipage-' . sha1(microtime());

    $page = new WikiPage([
        'title'      => $prefix . '-title',
        'slug'       => $prefix . '-slug',
        'project_id' => $project['id']
    ]);

    $revision = new WikiRevision([
        'content' => $prefix . '-content',
        'user_id' => $user['id']
    ]);

    $page->save();

    $revision['wiki_page_id'] = $page['id'];
    $revision->save();

    $page['revision_id'] = $revision['id'];
    $page->save();

    return $page;
}
