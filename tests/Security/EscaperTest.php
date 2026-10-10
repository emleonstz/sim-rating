<?php

declare(strict_types=1);

namespace Emleons\SimRating\Tests\Security;

use Emleons\SimRating\Security\Escaper;
use PHPUnit\Framework\TestCase;

class EscaperTest extends TestCase
{
    public function test_attr_escapes_html_special_characters(): void
    {
        $this->assertSame('&lt;script&gt;', Escaper::attr('<script>'));
        $this->assertSame('a&amp;b', Escaper::attr('a&b'));
        $this->assertSame('&quot;quoted&quot;', Escaper::attr('"quoted"'));
        $this->assertSame('&#039;single&#039;', Escaper::attr("'single'"));
    }

    public function test_color_accepts_safe_formats(): void
    {
        $this->assertSame('#ffc107', Escaper::color('#ffc107'));
        $this->assertSame('#fff', Escaper::color('#fff'));
        $this->assertSame('#ffc107aa', Escaper::color('#ffc107aa'));
        $this->assertSame('rgb(255, 0, 0)', Escaper::color('rgb(255, 0, 0)'));
        $this->assertSame('rgba(0,0,0,0.5)', Escaper::color('rgba(0,0,0,0.5)'));
        $this->assertSame('rebeccapurple', Escaper::color('rebeccapurple'));
        $this->assertSame('transparent', Escaper::color('transparent'));
    }

    public function test_color_rejects_dangerous_input_with_fallback(): void
    {
        $this->assertSame('#ffc107', Escaper::color('red; } body { display:none', '#ffc107'));
        $this->assertSame('#000000', Escaper::color('url(evil.png)', '#000000'));
        $this->assertSame('#ffc107', Escaper::color('</style><script>alert(1)</script>', '#ffc107'));
        $this->assertSame('#ffc107', Escaper::color(['array'], '#ffc107'));
    }

    public function test_length_accepts_safe_formats(): void
    {
        $this->assertSame('1em', Escaper::length('1em'));
        $this->assertSame('20px', Escaper::length('20px'));
        $this->assertSame('50%', Escaper::length('50%'));
        $this->assertSame('0', Escaper::length('0'));
    }

    public function test_length_rejects_dangerous_input_with_fallback(): void
    {
        $this->assertSame('1em', Escaper::length('10px; background:url(evil)', '1em'));
        $this->assertSame('1em', Escaper::length('</style>', '1em'));
    }

    public function test_css_strips_breaking_characters(): void
    {
        $result = Escaper::css('</style><script>alert(1)</script>');

        $this->assertStringNotContainsString('<', $result);
        $this->assertStringNotContainsString('>', $result);
        $this->assertStringNotContainsString('&', $result);
    }
}
