<?php

$testSuite->createGroup('Requests / Tickets / Update', function ($g) {
    $admin = createAdmin();
    $project = createProject();

    $g->test('New ticket form', function ($t) use ($project, $admin) {
        $resp = $t->visit('new_ticket', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Ticket</h1>', $resp->body);
    });
});
