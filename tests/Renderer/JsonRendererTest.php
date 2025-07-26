<?php

namespace SimRating\Tests\Renderer;

use Emleons\SimRating\Rating as SimRatingRating;
use Emleons\SimRating\Renderer\JsonRenderer as RendererJsonRenderer;
use PHPUnit\Framework\TestCase;
use SimRating\Rating;
use SimRating\Renderer\JsonRenderer;

class JsonRendererTest extends TestCase
{
    public function testRender()
    {
        $rating = new SimRatingRating([
            'one_star' => 1,
            'two_star' => 2,
            'three_star' => 3,
            'four_star' => 4,
            'five_star' => 5
        ]);

        $renderer = new RendererJsonRenderer($rating);
        $output = $renderer->render();
        $data = json_decode($output, true);

        $this->assertEquals(3.67, $data['average']);
        $this->assertEquals(15, $data['total']);
        $this->assertArrayHasKey('one_star', $data['distribution']);
    }

    public function testRenderWithEmptyRatings()
    {
        $rating = new SimRatingRating([]);
        $renderer = new RendererJsonRenderer($rating);
        $output = $renderer->render();
        $data = json_decode($output, true);

        $this->assertEquals(0, $data['average']);
        $this->assertEquals(0, $data['total']);
    }
}
