<?php

declare(strict_types=1);

namespace Emleons\SimRating\Tests\Renderer;

use Emleons\SimRating\Rating;
use Emleons\SimRating\Renderer\SvgRenderer;
use PHPUnit\Framework\TestCase;

class SvgRendererTest extends TestCase
{
    public function test_render(): void
    {
        $rating = new Rating(['five_star' => 1]);

        $output = (new SvgRenderer($rating))->render();

        $this->assertIsString($output);
        $this->assertStringStartsWith('<svg', $output);
        $this->assertStringContainsString('xmlns="http://www.w3.org/2000/svg"', $output);
        $this->assertStringContainsString('em-sim-rating', $output);
        $this->assertStringContainsString('em-sim-rating__svg', $output);
        $this->assertStringContainsString('sim-rating-svg', $output);
        $this->assertStringContainsString('data-sim-rating-id="', $output);
        $this->assertStringContainsString('role="img"', $output);
    }

    public function test_render_produces_five_stars(): void
    {
        $rating = new Rating([
            'one_star' => 1,
            'two_star' => 1,
            'three_star' => 1,
            'four_star' => 1,
            'five_star' => 1,
        ]);

        $output = (new SvgRenderer($rating))->render();

        // Five <g> star groups plus one gradient per filled star.
        $this->assertSame(5, substr_count($output, '<g transform='));
    }

    public function test_render_with_empty_ratings_has_no_gradients(): void
    {
        $output = (new SvgRenderer(new Rating([])))->render();

        $this->assertStringStartsWith('<svg', $output);
        $this->assertStringNotContainsString('linearGradient', $output);
    }
}
