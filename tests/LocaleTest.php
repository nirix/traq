<?php

namespace Traq\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Traq\Locale;

class LocaleTest extends TestCase
{
    private function locale(array $strings): Locale
    {
        $locale = new Locale();
        $locale->add($strings);

        return $locale;
    }

    public function testReturnsKeyWhenMissing(): void
    {
        $this->assertSame('does.not.exist', $this->locale([])->translate('does.not.exist'));
    }

    public function testExactMatchWithDottedKey(): void
    {
        $locale = $this->locale(['time.ago' => '{1} ago']);

        $this->assertSame('5 minutes ago', $locale->translate('time.ago', ['5 minutes']));
    }

    public function testNestedKey(): void
    {
        $locale = $this->locale(['a' => ['b' => 'nested']]);

        $this->assertSame('nested', $locale->translate('a.b'));
    }

    public function testParentKeyUsesFirstElement(): void
    {
        $locale = $this->locale(['timeline' => ['Timeline', 'by_x' => 'by {1}']]);

        $this->assertSame('Timeline', $locale->translate('timeline'));
        $this->assertSame('by Jack', $locale->translate('timeline.by_x', ['Jack']));
    }

    public function testVariablesAsArgumentsOrArray(): void
    {
        $locale = $this->locale(['greet' => 'Hello {1} and {2}']);

        $this->assertSame('Hello A and B', $locale->translate('greet', 'A', 'B'));
        $this->assertSame('Hello A and B', $locale->translate('greet', ['A', 'B']));
    }

    public function testNamedVariables(): void
    {
        $locale = $this->locale(['greet' => 'Hello {name}']);

        $this->assertSame('Hello Jack', $locale->translate('greet', ['name' => 'Jack']));
    }

    public static function plurals(): array
    {
        return [
            'one' => [1, '1 day'],
            'zero' => [0, '0 days'],
            'many' => [5, '5 days'],
            'negative one' => [-1, '-1 day'],
        ];
    }

    #[DataProvider('plurals')]
    public function testPlurals(int $count, string $expected): void
    {
        $locale = $this->locale(['days' => '{1} {plural:{1}, {day|days}}']);

        $this->assertSame($expected, $locale->translate('days', [$count]));
    }

    public function testAddOverridesExistingStrings(): void
    {
        $locale = $this->locale(['a' => 'old', 'b' => 'kept']);
        $locale->add(['a' => 'new']);

        $this->assertSame('new', $locale->translate('a'));
        $this->assertSame('kept', $locale->translate('b'));
    }

    public function testLoadRejectsUnknownAndTraversingNames(): void
    {
        $this->assertFalse(Locale::load('nope'));
        $this->assertFalse(Locale::load('../Locale/enUS'));
    }

    public function testLoadsEnUs(): void
    {
        $locale = Locale::load('enUS');

        $this->assertInstanceOf(Locale::class, $locale);
        $this->assertSame('Projects', $locale->translate('projects'));
        $this->assertSame('English', $locale::info()['name']);
    }
}
