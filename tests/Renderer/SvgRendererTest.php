<?php

namespace SimRating\Tests\Renderer;

use Emleons\SimRating\Rating as SimRatingRating;
use Emleons\SimRating\Renderer\SvgRenderer as RendererSvgRenderer;
use PHPUnit\Framework\TestCase;

class SvgRendererTest extends TestCase
{
    public function testRender()
    {
        $rating = new SimRatingRating([
            'five_star' => 1
        ]);

        $renderer = new RendererSvgRenderer($rating);
        $output = $renderer->render();

        $this->assertIsString($output);
        // Add more specific assertions when SVG implementation is complete
    }
}