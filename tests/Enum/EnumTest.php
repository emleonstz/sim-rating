<?php

declare(strict_types=1);

namespace Emleons\SimRating\Tests\Enum;

use Emleons\SimRating\Enum\DisplayType;
use Emleons\SimRating\Enum\StarKey;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function test_star_key_keys_are_ordered_and_complete(): void
    {
        $this->assertSame(
            ['one_star', 'two_star', 'three_star', 'four_star', 'five_star'],
            StarKey::keys()
        );
    }

    public function test_star_key_from_key_maps_to_integer_value(): void
    {
        $this->assertSame(1, StarKey::fromKey('one_star')->value);
        $this->assertSame(5, StarKey::fromKey('five_star')->value);
    }

    public function test_star_key_from_key_throws_on_unknown_key(): void
    {
        $this->expectException(InvalidArgumentException::class);

        StarKey::fromKey('nine_star');
    }

    public function test_star_key_key_round_trips(): void
    {
        $this->assertSame('three_star', StarKey::fromKey('three_star')->key());
    }

    public function test_display_type_from_string_is_case_insensitive(): void
    {
        $this->assertSame(DisplayType::Html, DisplayType::fromString('HTML'));
        $this->assertSame(DisplayType::Json, DisplayType::fromString(' json '));
    }

    public function test_display_type_from_string_throws_on_unknown_value(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DisplayType::fromString('xml');
    }
}
