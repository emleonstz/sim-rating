<?php

declare(strict_types=1);

namespace Emleons\SimRating\Renderer;

use Emleons\SimRating\Interfaces\RendererInterface;
use Emleons\SimRating\Rating;

class JsonRenderer implements RendererInterface
{
    protected Rating $rating;

    public function __construct(Rating $rating)
    {
        $this->rating = $rating;
    }

    public function render(): string
    {
        $distribution = array_map(
            static fn (float $v): float => round($v, 2),
            $this->rating->getDistribution()
        );

        $json = json_encode([
            'average' => $this->rating->getAverage(),
            'total' => $this->rating->getTotal(),
            'distribution' => $distribution,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);

        return $json === false ? '{}' : $json;
    }
}
