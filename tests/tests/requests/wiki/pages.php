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
});
