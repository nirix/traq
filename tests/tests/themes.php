<?php

use Traq\Themes;

$testSuite->createGroup('Themes API', function ($g) {
    $g->test('Register theme', function ($t) {
        $themeInfo = [
            'name' => 'Test Theme',
            'directory' => 'test'
        ];

        $selectOption = [
            'label' => $themeInfo['name'],
            'value' => $themeInfo['directory']
        ];

        $t->assertNotContains($selectOption, Themes::selectOptions());

        Themes::registerTheme($themeInfo);

        $t->assertContains($selectOption, Themes::selectOptions());
    });
});
