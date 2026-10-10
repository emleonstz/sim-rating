<?php

declare(strict_types=1);

namespace Emleons\SimRating\Tests\Renderer;

use Emleons\SimRating\Rating;
use Emleons\SimRating\Renderer\HtmlRenderer;
use PHPUnit\Framework\TestCase;

class HtmlRendererTest extends TestCase
{
    public function test_render(): void
    {
        $rating = new Rating([
            'one_star' => 0,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 1,
            'five_star' => 1,
        ]);

        $renderer = new HtmlRenderer($rating);
        $output = $renderer->render();

        $this->assertStringContainsString('data-rating-average="4.5"', $output);
        $this->assertStringContainsString('<svg', $output);
        $this->assertStringContainsString('d="M12 17.27', $output);
        $this->assertStringContainsString('role="img"', $output);
    }

    public function test_render_with_custom_options(): void
    {
        $rating = new Rating([
            'one_star' => 1,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 0,
            'five_star' => 0,
        ], [
            'color' => '#ff0000',
            'size' => '2em',
        ]);

        $renderer = new HtmlRenderer($rating);
        $output = $renderer->render();

        $this->assertStringContainsString('stop-color="#ff0000"', $output);
        $this->assertStringContainsString('width="2em"', $output);
        $this->assertStringContainsString('height="2em"', $output);
    }

    public function test_render_stars(): void
    {
        $rating = new Rating([
            'one_star' => 0,
            'two_star' => 0,
            'three_star' => 0,
            'four_star' => 1,
            'five_star' => 1,
        ]);

        $renderer = new HtmlRenderer($rating);
        $options = $rating->getOptions();

        $reflection = new \ReflectionClass($renderer);
        $method = $reflection->getMethod('renderStars');
        $method->setAccessible(true);

        $output = $method->invoke($renderer, 3.5, $options);

        $this->assertStringContainsString('<svg', $output);
        $this->assertStringContainsString('stop-color="#ffc107"', $output);
    }

    public function test_gradient_ids_are_unique_per_instance(): void
    {
        $ratings = ['five_star' => 1];

        $first = (new HtmlRenderer(new Rating($ratings)))->render();
        $second = (new HtmlRenderer(new Rating($ratings)))->render();

        preg_match_all('/id="([^"]+-grad1)"/', $first, $firstMatch);
        preg_match_all('/id="([^"]+-grad1)"/', $second, $secondMatch);

        $this->assertNotEmpty($firstMatch[1]);
        $this->assertNotEmpty($secondMatch[1]);
        $this->assertNotSame($firstMatch[1][0], $secondMatch[1][0]);
    }

    public function test_bars_renderer_outputs_structure(): void
    {
        $rating = new Rating([
            'one_star' => 1,
            'two_star' => 2,
            'three_star' => 3,
            'four_star' => 2,
            'five_star' => 2,
        ], ['type' => 'bars']);

        $output = (new HtmlRenderer($rating))->render();

        $this->assertStringContainsString('sim-rating-bars-container', $output);
        $this->assertStringContainsString('sim-rating-bar-fill', $output);
        $this->assertStringContainsString('5-star: 2', $output);
        $this->assertStringContainsString('sim-rating-summary', $output);
    }

    public function test_bars_summary_can_be_disabled(): void
    {
        $rating = new Rating(['five_star' => 1], [
            'type' => 'bars',
            'show_summary' => false,
        ]);

        $output = (new HtmlRenderer($rating))->render();

        $this->assertStringNotContainsString('<div class="sim-rating-summary">', $output);
        $this->assertStringNotContainsString('total ratings', $output);
    }

    public function test_interactive_mode_emits_script(): void
    {
        $rating = new Rating(['five_star' => 1], [
            'interactive' => true,
            'readonly' => false,
        ]);

        $output = (new HtmlRenderer($rating))->render();

        $this->assertStringContainsString('sim-rating:change', $output);
    }

    public function test_readonly_interactive_mode_emits_no_script(): void
    {
        $rating = new Rating(['five_star' => 1], [
            'interactive' => true,
            'readonly' => true,
        ]);

        $output = (new HtmlRenderer($rating))->render();

        $this->assertStringNotContainsString('sim-rating:change', $output);
    }
}
