<?php

namespace Traq\Tests\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SlugTest extends TestCase
{
    public static function slugs(): array
    {
        return [
            'spaces become hyphens' => ['hello world', 'hello-world'],
            'uppercase is lowercased' => ['Hello World', 'hello-world'],
            'special characters stripped' => ['foo!@#bar', 'foobar'],
            'allowed punctuation kept' => ['a-b_c.d', 'a-b_c.d'],
            'empty string' => ['', ''],
        ];
    }

    #[DataProvider('slugs')]
    public function testMkSlug(string $input, string $expected): void
    {
        $this->assertSame($expected, mkSlug($input));
    }
}
