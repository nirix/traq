<?php

$testSuite->createGroup('Requests / Wiki / Pages', function ($g) {
    $project = createProject();
    $wikiPage = createWikiPage($project);

    $g->test('List pages', function ($t) use ($project, $wikiPage) {
        $resp = $t->visit('wiki_pages', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Pages</h1>', $resp->body);
        $t->assertContains($wikiPage['title'], $resp->body);
    });

    $g->test('Create page', function ($t) use ($project) {
        $admin = createAdmin();

        $resp = $t->visit('wiki_create', [
            'method' => 'POST',
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'post' => [
                'title' => 'My Page',
                'slug' => 'my-page',
                'content' => 'My Wiki Page'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $intendedUrl = $t->generateUrl('wiki_page', [
            'pslug' => $project['slug'],
            'wslug'  => 'my-page'
        ]);

        $t->assertRedirectTo($intendedUrl, $resp);
    });
});
