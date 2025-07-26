<?php

namespace SimRating\Tests;

use PHPUnit\Framework\TestCase;
use Emleons\SimRating\Rating;
use InvalidArgumentException;

class RatingTest extends TestCase
{
    public function testValidInitialization()
    {
        $ratings = [
            'one_star' => 10,
            'two_star' => 20,
            'three_star' => 30,
            'four_star' => 40,
            'five_star' => 50
        ];

        $rating = new Rating($ratings);
        $this->assertInstanceOf(Rating::class, $rating);
    }

    public function testInvalidRatingKeyThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);

        new Rating([
            'invalid_key' => 10,
            'two_star' => 20
        ]);
    }

    public function testNegativeRatingCountThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);

        new Rating([
            'one_star' => -1,
            'two_star' => 20
        ]);
    }

    public function testAverageCalculation()
    {
        $rating = new Rating([
            'one_star' => 0,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 1,
            'five_star' => 1
        ]);

        $this->assertEquals(4.5, $rating->getAverage());
    }

    public function testZeroRatingsAverage()
    {
        $rating = new Rating([
            'one_star' => 0,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 0,
            'five_star' => 0
        ]);

        $this->assertEquals(0, $rating->getAverage());
    }

    public function testTotalRatingsCalculation()
    {
        $rating = new Rating([
            'one_star' => 5,
            'two_star' => 10,
            'three_star' => 15,
            'four_star' => 20,
            'five_star' => 25
        ]);

        $this->assertEquals(75, $rating->getTotal());
    }

    public function testRatingDistribution()
    {
        $rating = new Rating([
            'one_star' => 1,
            'two_star' => 2,
            'three_star' => 2,
            'four_star' => 3,
            'five_star' => 2
        ]);

        $expected = [
            'one_star' => 10.0,
            'two_star' => 20.0,
            'three_star' => 20.0,
            'four_star' => 30.0,
            'five_star' => 20.0
        ];

        $this->assertEquals($expected, $rating->getDistribution());
    }

    public function testDefaultOptions()
    {
        $rating = new Rating([]);
        $options = $rating->getOptions();

        $this->assertArrayHasKey('type', $options);
        $this->assertArrayHasKey('color', $options);
        $this->assertArrayHasKey('size', $options);
    }

    public function testCustomOptions()
    {
        $customOptions = [
            'color' => '#ff0000',
            'size' => '2em'
        ];

        $rating = new Rating([], $customOptions);
        $options = $rating->getOptions();

        $this->assertEquals('#ff0000', $options['color']);
        $this->assertEquals('2em', $options['size']);
    }

    public function testSetRatings()
    {
        $rating = new Rating([]);
        $rating->setRatings([
            'one_star' => 1,
            'five_star' => 1
        ]);

        $this->assertEquals(3.0, $rating->getAverage());
    }

    public function testSetOptions()
    {
        $rating = new Rating([]);
        $rating->setOptions(['color' => '#00ff00']);

        $this->assertEquals('#00ff00', $rating->getOptions()['color']);
    }
}
