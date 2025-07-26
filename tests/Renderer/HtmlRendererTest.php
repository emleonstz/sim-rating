<?php

namespace SimRating\Tests\Renderer;

use Emleons\SimRating\Rating as SimRatingRating;
use Emleons\SimRating\Renderer\HtmlRenderer as RendererHtmlRenderer;
use PHPUnit\Framework\TestCase;
use SimRating\Rating;
use SimRating\Renderer\HtmlRenderer;

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

        $renderer = new RendererHtmlRenderer($rating);
        $output = $renderer->render();

        $this->assertStringContainsString('data-rating-average="4.5"', $output);
        $this->assertStringContainsString('<svg', $output); // Check for SVG instead of fa-star
        $this->assertStringContainsString('d="M12 17.27', $output); // Check for star path
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

        $renderer = new RendererHtmlRenderer($rating);
        $output = $renderer->render();

        $this->assertStringContainsString('stop-color="#ff0000"', $output);
        $this->assertStringContainsString('width="2em"', $output);
        $this->assertStringContainsString('height="2em"', $output);
    }

    public function testRenderStars()
    {
        $rating = new SimRatingRating([]);
        $renderer = new RendererHtmlRenderer($rating);

        // Test reflection to access protected method
        $reflection = new \ReflectionClass($renderer);
        $method = $reflection->getMethod('renderStars');
        $method->setAccessible(true);

        $output = $method->invoke($renderer, 3.5);
        $this->assertStringContainsString('<svg', $output);
        $this->assertStringContainsString('stop-color="#ffc107"', $output);
    }
}