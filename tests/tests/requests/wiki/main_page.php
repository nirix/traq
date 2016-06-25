<?php

use Traq\Models\Project;

$testSuite->createGroup('Requests / Wiki / Main Page', function ($g) {
    $project = Project::select()->fetch() ?: createProject();

    $project['enable_wiki'] = true;
    $project->save();

    $g->test('No main page', function ($t) use ($project) {
        $resp = $t->visit('wiki', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);
        $t->assertContains('<h1 class="page-header">New Page</h1>', $resp->body);
    });

    $g->test('Create main page', function ($t) use ($project) {
        $admin = createAdmin();

        $resp = $t->visit('wiki_create', [
            'method' => 'POST',
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'post' => [
                'title' => 'Main Page',
                'slug' => 'main',
                'content' => 'Main Wiki Page'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $indendedUrl = $t->generateUrl('wiki_page', [
            'pslug' => $project['slug'],
            'slug'  => 'main'
        ]);

        $t->assertRedirectTo($indendedUrl, $resp);
    });

    $g->test('Show main page', function ($t) use ($project) {
        $resp = $t->visit('wiki', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Main Page</h1>', $resp->body);
    });
});
