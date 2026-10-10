<?php

declare(strict_types=1);

namespace Emleons\SimRating\Renderer;

use Emleons\SimRating\Interfaces\RendererInterface;
use Emleons\SimRating\Rating;
use Emleons\SimRating\Renderer\Concerns\InstanceAware;
use Emleons\SimRating\Security\Escaper;

/**
 * Renders a self-contained, standalone <svg> of the rating.
 */
class SvgRenderer implements RendererInterface
{
    use InstanceAware;

    protected Rating $rating;

    protected string $uid;

    public function __construct(Rating $rating)
    {
        $this->rating = $rating;
        $this->uid = 'simrating'.bin2hex(random_bytes(4));
    }

    public function render(): string
    {
        $options = $this->rating->getOptions();
        $average = $this->rating->getAverage();
        $total = $this->rating->getTotal();

        $color = Escaper::color($options['color']);
        $emptyColor = Escaper::color($options['empty_color'] ?? '#ddd');
        [$sizeNumber, $sizeUnit] = $this->parseLength((string) $options['size'], 24.0);

        $starSize = (int) round($sizeNumber);
        $totalWidth = $starSize * 5;
        $ariaLabel = sprintf('Rated %s out of 5 from %d ratings', number_format($average, 1), $total);

        $defs = '';
        $stars = '';
        for ($position = 1; $position <= 5; $position++) {
            $fill = max(0.0, min(1.0, max(0.0, min(5.0, $average)) - ($position - 1)));
            $x = ($position - 1) * $starSize;

            if ($fill > 0.0) {
                $gradientId = $this->uid.'-g'.$position;
                $offset = $fill >= 1.0 ? '100%' : round($fill * 100).'%';
                $defs .= '<linearGradient id="'.$gradientId.'">'
                    .'<stop offset="'.$offset.'" stop-color="'.$color.'"/>'
                    .'<stop offset="'.$offset.'" stop-color="transparent"/></linearGradient>';
                $fillAttr = 'url(#'.$gradientId.')';
            } else {
                $fillAttr = $emptyColor;
            }

            $stars .= '<g transform="translate('.$x.',0)"><path fill="'.$fillAttr
                .'" d="'.$this->starPath().'"/></g>';
        }

        return '<svg '.$this->classes(
            'em-sim-rating',
            'em-sim-rating__svg',
            'em-sim-rating--'.$this->instanceSlug($options),
            'sim-rating-svg'
        ).' xmlns="http://www.w3.org/2000/svg" width="'.$totalWidth
            .'" height="'.$starSize.'" viewBox="0 0 '.$totalWidth.' '.$starSize
            .'" data-sim-rating-id="'.Escaper::attr($this->instanceId($options)).'"'
            .' role="img" aria-label="'.Escaper::attr($ariaLabel).'">'
            .'<defs>'.$defs.'</defs>'
            .$stars
            .'</svg>';
    }

    /**
     * The star glyph path on a 24x24 canvas.
     */
    protected function starPath(): string
    {
        return 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z';
    }

    /**
     * Extract a leading numeric value from a CSS length (e.g. "2em" => 2).
     *
     * @return array{0: float, 1: string}
     */
    protected function parseLength(string $value, float $defaultNumber): array
    {
        if (preg_match('/^(\d+(?:\.\d+)?)(px|em|rem|pt|ex|ch|%)?$/i', trim($value), $m) === 1) {
            return [(float) $m[1], strtolower($m[2] ?? 'px')];
        }

        return [$defaultNumber, 'px'];
    }
}
