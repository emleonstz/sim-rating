<?php

declare(strict_types=1);

namespace Emleons\SimRating\Tests\Renderer;

use Emleons\SimRating\Rating;
use Emleons\SimRating\Renderer\JsonRenderer;
use PHPUnit\Framework\TestCase;

class JsonRendererTest extends TestCase
{
    public function test_render(): void
    {
        $rating = new Rating([
            'one_star' => 1,
            'two_star' => 2,
            'three_star' => 3,
            'four_star' => 4,
            'five_star' => 5,
        ]);

        $renderer = new JsonRenderer($rating);
        $output = $renderer->render();
        $data = json_decode($output, true);

        $this->assertSame(3.67, $data['average']);
        $this->assertSame(15, $data['total']);
        $this->assertArrayHasKey('one_star', $data['distribution']);
    }

    public function test_render_with_empty_ratings(): void
    {
        $rating = new Rating([]);
        $renderer = new JsonRenderer($rating);
        $data = json_decode($renderer->render(), true);

        $this->assertSame(0.0, $data['average']);
        $this->assertSame(0, $data['total']);
    }
}
