<?php

namespace Tests\Unit;

use App\Support\Slugger;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SluggerTest extends TestCase
{
    public function test_it_creates_slug_from_english_title(): void
    {
        $this->assertSame(
            'iPhone-13-Pro-Max',
            Slugger::make('iPhone 13 Pro Max')
        );
    }

    public function test_it_creates_slug_from_persian_title(): void
    {
        $this->assertSame(
            'آیفون-۱۳-پرو-مکس',
            Slugger::make('آیفون ۱۳ پرو مکس')
        );
    }

    public function test_it_creates_slug_from_mixed_english_and_persian_title(): void
    {
        $this->assertSame(
            'فروش-iPhone-13',
            Slugger::make('فروش iPhone 13')
        );
    }

    #[DataProvider('titleSlugProvider')]
    public function test_it_replaces_spaces_with_separator(string $title, string $expected): void
    {
        $this->assertSame($expected, Slugger::make($title));
    }

    public static function titleSlugProvider(): array
    {
        return [
            'english with punctuation' => ['Hello, World!', 'Hello-World'],
            'persian with punctuation' => ['خانه زیبا!', 'خانه-زیبا'],
            'multiple spaces' => ['foo   bar', 'foo-bar'],
        ];
    }
}
