<?php

namespace SimRating\Tests\Renderer;

use Emleons\SimRating\Rating as SimRatingRating;
use PHPUnit\Framework\TestCase;
use SimRating\Rating;
use Emleons\SimRating\Renderer\HtmlRenderer;

class HtmlRendererTest extends TestCase
{
    public function testRender()
    {
        $rating = new SimRatingRating([
            'one_star' => 0,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 1,
            'five_star' => 1
        ]);

        $renderer = new HtmlRenderer($rating);
        $output = $renderer->render();

        $this->assertStringContainsString('data-rating-average="4.5"', $output);
        $this->assertStringContainsString('fa-star', $output);
    }

    public function testRenderWithCustomOptions()
    {
        $rating = new SimRatingRating([
            'one_star' => 1,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 0,
            'five_star' => 0
        ], [
            'color' => '#ff0000',
            'size' => '2em'
        ]);

        $renderer = new HtmlRenderer($rating);
        $output = $renderer->render();

        // Should find the custom color in full stars
        $this->assertStringContainsString('color: #ff0000', $output);
        $this->assertStringContainsString('font-size: 2em', $output);
    }

    public function testRenderStars()
    {
        $rating = new SimRatingRating([]);
        $renderer = new HtmlRenderer($rating);

        // Test reflection to access protected method
        $reflection = new \ReflectionClass($renderer);
        $method = $reflection->getMethod('renderStars');
        $method->setAccessible(true);

        $this->assertStringContainsString('fa-star', $method->invoke($renderer, 3.5));
    }
}
