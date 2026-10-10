<?php

declare(strict_types=1);

namespace Emleons\SimRating\Tests;

use Emleons\SimRating\Enum\DisplayType;
use Emleons\SimRating\Enum\StarKey;
use Emleons\SimRating\Rating;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RatingTest extends TestCase
{
    public static function ratingProvider(): array
    {
        return [
            'all buckets' => [[
                'one_star' => 10,
                'two_star' => 20,
                'three_star' => 30,
                'four_star' => 40,
                'five_star' => 50,
            ]],
            'single bucket' => [['five_star' => 3]],
            'empty' => [[]],
        ];
    }

    public function test_valid_initialization(): void
    {
        $rating = new Rating([
            'one_star' => 10,
            'two_star' => 20,
            'three_star' => 30,
            'four_star' => 40,
            'five_star' => 50,
        ]);

        $this->assertInstanceOf(Rating::class, $rating);
    }

    #[DataProvider('ratingProvider')]
    public function test_ratings_are_normalised_to_all_buckets(array $ratings): void
    {
        $rating = new Rating($ratings);

        $this->assertSame(StarKey::keys(), array_keys($rating->getRatings()));
        foreach ($rating->getRatings() as $count) {
            $this->assertIsInt($count);
        }
    }

    public function test_invalid_rating_key_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid rating key: invalid_key');

        new Rating(['invalid_key' => 10, 'two_star' => 20]);
    }

    public function test_negative_rating_count_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Rating(['one_star' => -1, 'two_star' => 20]);
    }

    public function test_non_integer_rating_count_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Rating(['one_star' => '10', 'two_star' => 20]);
    }

    public function test_strict_mode_requires_all_keys(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required rating key');

        new Rating(['one_star' => 1], ['strict' => true]);
    }

    public function test_strict_mode_accepts_all_keys(): void
    {
        $rating = new Rating(array_fill_keys(StarKey::keys(), 1), ['strict' => true]);

        $this->assertSame(3.0, $rating->getAverage());
    }

    public function test_average_calculation(): void
    {
        $rating = new Rating([
            'one_star' => 0,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 1,
            'five_star' => 1,
        ]);

        $this->assertSame(4.5, $rating->getAverage());
    }

    public function test_zero_ratings_average(): void
    {
        $rating = new Rating([
            'one_star' => 0,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 0,
            'five_star' => 0,
        ]);

        $this->assertSame(0.0, $rating->getAverage());
    }

    public function test_total_ratings_calculation(): void
    {
        $rating = new Rating([
            'one_star' => 5,
            'two_star' => 10,
            'three_star' => 15,
            'four_star' => 20,
            'five_star' => 25,
        ]);

        $this->assertSame(75, $rating->getTotal());
    }

    public function test_rating_distribution(): void
    {
        $rating = new Rating([
            'one_star' => 1,
            'two_star' => 2,
            'three_star' => 2,
            'four_star' => 3,
            'five_star' => 2,
        ]);

        $expected = [
            'one_star' => 10.0,
            'two_star' => 20.0,
            'three_star' => 20.0,
            'four_star' => 30.0,
            'five_star' => 20.0,
        ];

        $this->assertSame($expected, $rating->getDistribution());
    }

    public function test_default_options(): void
    {
        $rating = new Rating([]);
        $options = $rating->getOptions();

        $this->assertArrayHasKey('type', $options);
        $this->assertArrayHasKey('color', $options);
        $this->assertArrayHasKey('size', $options);
        $this->assertArrayHasKey('strict', $options);
        $this->assertArrayHasKey('bar_height', $options);
    }

    public function test_custom_options(): void
    {
        $customOptions = [
            'color' => '#ff0000',
            'size' => '2em',
        ];

        $rating = new Rating([], $customOptions);
        $options = $rating->getOptions();

        $this->assertSame('#ff0000', $options['color']);
        $this->assertSame('2em', $options['size']);
    }

    public function test_set_ratings(): void
    {
        $rating = new Rating([]);
        $rating->setRatings([
            'one_star' => 1,
            'five_star' => 1,
        ]);

        $this->assertSame(3.0, $rating->getAverage());
    }

    public function test_set_options(): void
    {
        $rating = new Rating([]);
        $rating->setOptions(['color' => '#00ff00']);

        $this->assertSame('#00ff00', $rating->getOptions()['color']);
    }

    public function test_render_accepts_string_type(): void
    {
        $rating = new Rating(['five_star' => 1]);

        $this->assertJson($rating->render('json'));
    }

    public function test_render_accepts_enum_type(): void
    {
        $rating = new Rating(['five_star' => 1]);

        $this->assertJson($rating->render(DisplayType::Json));
    }

    public function test_render_rejects_unknown_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported display type');

        (new Rating([]))->render('xml');
    }

    public function test_render_per_call_options_do_not_mutate_instance(): void
    {
        $rating = new Rating(['five_star' => 1]);

        $output = $rating->render('html', ['color' => '#123456', 'type' => 'bars']);

        $this->assertStringContainsString('sim-rating-bars-container', $output);
        $this->assertSame('#ffc107', $rating->getOptions()['color']);
        $this->assertSame('stars', $rating->getOptions()['type']);
    }
}
