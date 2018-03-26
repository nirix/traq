<?php

namespace Tests\Requests\Wiki;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;

class MainPageTest extends TestCase
{
    protected static $project;

    /**
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$project = createProject();

        static::$project['enable_wiki'] = true;
        static::$project->save();
    }

    public function testNoMainPage()
    {
        $resp = $this->visit('wiki', [
            'routeTokens' => [
                'pslug' => static::$project->slug
            ]
        ]);

        $this->assertContains('<h1 class="page-header">New Page</h1>', $resp->body);
    }

    public function testCreateMainPage()
    {
        $admin = createAdmin();

        $resp = $this->visit('wiki_create', [
            'method' => 'POST',
            'routeTokens' => [
                'pslug' => static::$project['slug']
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

        $intendedUrl = $this->generateUrl('wiki_page', [
            'pslug' => static::$project['slug'],
            'wslug'  => 'main'
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($intendedUrl, $resp->url);
    }

    public function testShowMainPage()
    {
        $resp = $this->visit('wiki', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Main Page</h1>', $resp->body);
    }
}
