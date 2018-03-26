<?php
namespace Tests;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Themes;

class ThemesTest extends TestCase
{
    public function testRegisterTheme()
    {
        $themeInfo = [
            'name' => 'Test Theme',
            'directory' => 'test'
        ];

        $selectOption = [
            'label' => $themeInfo['name'],
            'value' => $themeInfo['directory']
        ];

        $this->assertNotContains($selectOption, Themes::selectOptions());

        Themes::registerTheme($themeInfo);

        $this->assertContains($selectOption, Themes::selectOptions());
    }
}
