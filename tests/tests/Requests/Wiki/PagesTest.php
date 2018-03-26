<?php

namespace Tests\Requests\Wiki;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Project;
use Traq\Models\WikiPage;

class PagesTest extends TestCase
{
    /**
     * @var Project
     */
    protected static $project;

    /**
     * @var WikiPage
     */
    protected static $page;

    /**
     * @beforeClass
     */
    public static function setUpPage()
    {
        static::$project = createProject();
        static::$page = createWikiPage(static::$project);
    }

    public function testListPages()
    {
        $resp = $this->visit('wiki_pages', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Pages</h1>', $resp->body);
        $this->assertContains(static::$page['title'], $resp->body);
    }

    public function testCreatePage()
    {
        $admin = createAdmin();

        $resp = $this->visit('wiki_create', [
            'method' => 'POST',
            'routeTokens' => [
                'pslug' => static::$project['slug']
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

        $intendedUrl = $this->generateUrl('wiki_page', [
            'pslug' => static::$project['slug'],
            'wslug'  => 'my-page'
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($intendedUrl, $resp->url);
    }
}
