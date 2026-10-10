<?php

declare(strict_types=1);

namespace Emleons\SimRating\Enum;

/**
 * Backed enum representing the five rating buckets.
 *
 * Each case maps a human-friendly bucket key (e.g. "one_star") to the integer
 * number of stars it represents (1-5).
 */
enum StarKey: int
{
    case OneStar = 1;
    case TwoStar = 2;
    case ThreeStar = 3;
    case FourStar = 4;
    case FiveStar = 5;

    /**
     * Ordered list of the canonical bucket keys ("one_star" ... "five_star").
     *
     * @return list<string>
     */
    public static function keys(): array
    {
        return ['one_star', 'two_star', 'three_star', 'four_star', 'five_star'];
    }

    /**
     * Resolve a bucket key (e.g. "one_star") to its enum case.
     */
    public static function fromKey(string $key): self
    {
        $byKey = [
            'one_star' => self::OneStar,
            'two_star' => self::TwoStar,
            'three_star' => self::ThreeStar,
            'four_star' => self::FourStar,
            'five_star' => self::FiveStar,
        ];

        return $byKey[$key] ?? throw new \InvalidArgumentException("Invalid rating key: {$key}");
    }

    /**
     * The canonical bucket key for this case (e.g. "three_star").
     */
    public function key(): string
    {
        return match ($this) {
            self::OneStar => 'one_star',
            self::TwoStar => 'two_star',
            self::ThreeStar => 'three_star',
            self::FourStar => 'four_star',
            self::FiveStar => 'five_star',
        };
    }
}
