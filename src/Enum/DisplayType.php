<?php

declare(strict_types=1);

namespace Emleons\SimRating\Enum;

/**
 * The output formats supported by the library.
 */
enum DisplayType: string
{
    case Html = 'html';
    case Json = 'json';
    case Svg = 'svg';

    /**
     * Coerce a user supplied string into a DisplayType.
     *
     * @throws \InvalidArgumentException when the value is not a known format.
     */
    public static function fromString(string $value): self
    {
        return self::tryFrom(strtolower(trim($value)))
            ?? throw new \InvalidArgumentException("Unsupported display type: {$value}");
    }
}
