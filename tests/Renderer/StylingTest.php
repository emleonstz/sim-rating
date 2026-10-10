<?php

declare(strict_types=1);

namespace Emleons\SimRating\Tests\Renderer;

use Emleons\SimRating\Rating;
use PHPUnit\Framework\TestCase;

class StylingTest extends TestCase
{
    public function test_star_root_has_bem_and_legacy_classes(): void
    {
        $output = (new Rating(['five_star' => 1]))->render('html');

        $this->assertStringContainsString('class="em-sim-rating em-sim-rating--', $output);
        $this->assertStringContainsString('sim-rating"', $output);
        $this->assertStringContainsString('em-sim-rating__stars', $output);
        $this->assertStringContainsString('em-sim-rating__average', $output);
        $this->assertStringContainsString('sim-rating-average', $output);
    }

    public function test_star_elements_carry_bem_and_legacy_classes(): void
    {
        $output = (new Rating(['five_star' => 1]))->render('html');

        $this->assertStringContainsString('class="em-sim-rating__star sim-rating-star"', $output);
    }

    public function test_instance_id_is_used_for_data_attribute_and_scope(): void
    {
        $output = (new Rating(['five_star' => 1], ['instance_id' => 'product-42']))->render('html');

        $this->assertStringContainsString('data-sim-rating-id="product-42"', $output);
        $this->assertStringContainsString('em-sim-rating--product-42', $output);
    }

    public function test_two_instances_have_isolated_scopes(): void
    {
        $first = (new Rating(['five_star' => 1], ['type' => 'bars', 'instance_id' => 'alpha']))->render('html');
        $second = (new Rating(['five_star' => 1], ['type' => 'bars', 'instance_id' => 'beta']))->render('html');

        $this->assertStringContainsString('.em-sim-rating--alpha .em-sim-rating__bar{', $first);
        $this->assertStringContainsString('.em-sim-rating--beta .em-sim-rating__bar{', $second);

        // Neither instance's scoped CSS leaks into the other.
        $this->assertStringNotContainsString('.em-sim-rating--beta', $first);
        $this->assertStringNotContainsString('.em-sim-rating--alpha', $second);
    }

    public function test_inline_styles_can_be_disabled(): void
    {
        $output = (new Rating(['five_star' => 1], [
            'type' => 'bars',
            'inline_styles' => false,
        ]))->render('html');

        $this->assertStringNotContainsString('<style>', $output);
        // Structure is still present.
        $this->assertStringContainsString('em-sim-rating__bar-fill', $output);
    }

    public function test_inline_styles_enabled_by_default_and_scoped(): void
    {
        $output = (new Rating(['five_star' => 1], ['type' => 'bars']))->render('html');

        $this->assertStringContainsString('<style>', $output);
        // Default CSS is scoped to the instance, never a bare global selector.
        $this->assertStringNotContainsString('.sim-rating-bar{', $output);
        $this->assertMatchesRegularExpression('/\.em-sim-rating--[a-z0-9_]+ \.em-sim-rating__bar\{/', $output);
    }

    public function test_bar_elements_carry_bem_and_legacy_classes(): void
    {
        $output = (new Rating(['five_star' => 1], ['type' => 'bars']))->render('html');

        $this->assertStringContainsString('em-sim-rating__bars', $output);
        $this->assertStringContainsString('sim-rating-bars-container', $output);
        $this->assertStringContainsString('class="em-sim-rating__bar sim-rating-bar"', $output);
        $this->assertStringContainsString('class="em-sim-rating__bar-label sim-rating-bar-label"', $output);
        $this->assertStringContainsString('class="em-sim-rating__bar-track sim-rating-bar-bg"', $output);
        $this->assertStringContainsString('class="em-sim-rating__bar-fill sim-rating-bar-fill"', $output);
        $this->assertStringContainsString('class="em-sim-rating__summary sim-rating-summary"', $output);
    }

    public function test_malicious_instance_id_is_sanitised_in_class_but_preserved_in_data_attribute(): void
    {
        $output = (new Rating(['five_star' => 1], ['instance_id' => 'a b" onx="1']))->render('html');

        // The scope class only contains safe characters.
        $this->assertMatchesRegularExpression('/em-sim-rating--[a-zA-Z0-9_-]+/', $output);
        $this->assertStringNotContainsString('em-sim-rating--a b', $output);
        $this->assertStringNotContainsString('onx="1', $output);
        // The data attribute value is HTML-escaped (quotes neutralised).
        $this->assertStringContainsString('data-sim-rating-id="a b&quot; onx=&quot;1"', $output);
    }
}
