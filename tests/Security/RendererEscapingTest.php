<?php

declare(strict_types=1);

namespace Emleons\SimRating\Tests\Security;

use Emleons\SimRating\Rating;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RendererEscapingTest extends TestCase
{
    /**
     * Payloads that must never appear verbatim in rendered markup.
     *
     * @return array<string, array{string}>
     */
    public static function xssPayloads(): array
    {
        return [
            'script tag' => ['"><script>alert(1)</script>'],
            'img onerror' => ['"><img src=x onerror=alert(1)>'],
            'svg onload' => ['"><svg onload=alert(1)>'],
            'style breakout' => ['#fff; } body { display:none } /*'],
            'attribute break' => ['" onmouseover="alert(1)'],
        ];
    }

    #[DataProvider('xssPayloads')]
    public function test_star_renderer_neutralises_malicious_color(string $payload): void
    {
        $rating = new Rating(['five_star' => 1], ['color' => $payload]);

        $output = $rating->render('html');

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringNotContainsString('onerror=', $output);
        $this->assertStringNotContainsString('onmouseover=', $output);
        // Falls back to the safe default colour.
        $this->assertStringContainsString('#ffc107', $output);
    }

    #[DataProvider('xssPayloads')]
    public function test_star_renderer_neutralises_malicious_size(string $payload): void
    {
        $rating = new Rating(['five_star' => 1], ['size' => $payload]);

        $output = $rating->render('html');

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringNotContainsString('onerror=', $output);
    }

    #[DataProvider('xssPayloads')]
    public function test_bars_renderer_neutralises_malicious_color(string $payload): void
    {
        $rating = new Rating(['five_star' => 1], [
            'type' => 'bars',
            'color' => $payload,
        ]);

        $output = $rating->render('html');

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringNotContainsString('onerror=', $output);
        $this->assertStringNotContainsString('onmouseover=', $output);
        // The raw payload must never leak into the markup.
        $this->assertStringNotContainsString($payload, $output);
        // Colour falls back to the safe default.
        $this->assertStringContainsString('#ffc107', $output);
    }
}
