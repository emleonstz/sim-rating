<?php

declare(strict_types=1);

namespace Emleons\SimRating\Renderer;

use Emleons\SimRating\Interfaces\RendererInterface;
use Emleons\SimRating\Rating;
use Emleons\SimRating\Renderer\Concerns\InstanceAware;
use Emleons\SimRating\Security\Escaper;

class HtmlRenderer implements RendererInterface
{
    use InstanceAware;

    protected Rating $rating;

    /**
     * A unique prefix for this renderer instance so that multiple ratings on a
     * single page never generate colliding SVG gradient IDs.
     */
    protected string $uid;

    public function __construct(Rating $rating)
    {
        $this->rating = $rating;
        $this->uid = 'simrating'.bin2hex(random_bytes(4));
    }

    public function render(): string
    {
        $options = $this->rating->getOptions();

        return $options['type'] === 'bars'
            ? $this->renderBars($options)
            : $this->renderDefault($options);
    }

    /**
     * @param  array<string,  mixed>  $options
     */
    protected function renderDefault(array $options): string
    {
        $average = $this->rating->getAverage();
        $total = $this->rating->getTotal();
        $averageText = number_format($average, 1);
        $ariaLabel = sprintf('Rated %s out of 5 from %d ratings', $averageText, $total);

        $html = '<div '.$this->classes('em-sim-rating', 'em-sim-rating--'.$this->instanceSlug($options), 'sim-rating')
            .' data-rating-average="'.Escaper::attr((string) $average).'"'
            .' data-sim-rating-id="'.Escaper::attr($this->instanceId($options)).'"'
            .' role="img" aria-label="'.Escaper::attr($ariaLabel).'">';

        $html .= '<div '.$this->classes('em-sim-rating__stars').'>';
        $html .= $this->renderStars($average, $options);
        $html .= '</div>';

        if ($options['show_average']) {
            $html .= '<span '.$this->classes('em-sim-rating__average', 'sim-rating-average').'>'
                .Escaper::attr($averageText).'</span>';
        }

        if ($options['show_total']) {
            $html .= '<span '.$this->classes('em-sim-rating__total', 'sim-rating-total').'>('
                .Escaper::attr((string) $total).' ratings)</span>';
        }

        $html .= '</div>';

        if ($options['interactive'] && ! $options['readonly']) {
            $html .= $this->renderInteractiveScript();
        }

        return $html;
    }

    /**
     * Render the five stars, filling each proportionally to the average.
     *
     * @param  array<string,  mixed>  $options
     */
    protected function renderStars(float $average, array $options): string
    {
        $color = Escaper::color($options['color']);
        $emptyColor = Escaper::color($options['empty_color'] ?? '#ddd');
        $size = Escaper::length($options['size']);

        $clamped = max(0.0, min(5.0, $average));
        $output = '';

        for ($position = 1; $position <= 5; $position++) {
            $fill = max(0.0, min(1.0, $clamped - ($position - 1)));
            $output .= $this->renderStar($position, $fill, $color, $emptyColor, $size);
        }

        return $output;
    }

    /**
     * Render a single star with a proportional gradient fill.
     *
     * @param  float  $fill  Fraction of the star to fill, between 0.0 and 1.0.
     */
    protected function renderStar(int $position, float $fill, string $color, string $emptyColor, string $size): string
    {
        $path = 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z';
        $value = Escaper::attr((string) $position);
        $starClass = $this->classes('em-sim-rating__star', 'sim-rating-star');

        if ($fill <= 0.0) {
            // Empty star: no gradient required.
            return '<svg '.$starClass.' width="'.$size.'" height="'.$size
                .'" viewBox="0 0 24 24" data-rating-value="'.$value.'" aria-hidden="true">'
                .'<path fill="'.$emptyColor.'" d="'.$path.'"/></svg>';
        }

        $gradientId = $this->uid.'-grad'.$position;
        $offset = $fill >= 1.0 ? '100%' : round($fill * 100).'%';

        return '<svg '.$starClass.' width="'.$size.'" height="'.$size
            .'" viewBox="0 0 24 24" data-rating-value="'.$value.'" aria-hidden="true">'
            .'<defs><linearGradient id="'.$gradientId.'">'
            .'<stop offset="'.$offset.'" stop-color="'.$color.'"/>'
            .'<stop offset="'.$offset.'" stop-color="transparent"/>'
            .'</linearGradient></defs>'
            .'<path fill="url(#'.$gradientId.')" stroke="'.$color.'" stroke-width="1" d="'.$path.'"/>'
            .'</svg>';
    }

    /**
     * A tiny, dependency-free progressive-enhancement script that emits a
     * "sim-rating:change" event carrying the selected star value. No eval,
     * no network, no user data interpolated into the script body.
     */
    protected function renderInteractiveScript(): string
    {
        return '<script>(function(){'
            .'var root=document.currentScript&&document.currentScript.previousElementSibling;'
            .'if(!root)return;'
            .'root.addEventListener("click",function(ev){'
            .'var star=ev.target.closest("[data-rating-value]");'
            .'if(!star)return;'
            .'root.dispatchEvent(new CustomEvent("sim-rating:change",{bubbles:true,'
            .'detail:{value:parseInt(star.getAttribute("data-rating-value"),10)}}));'
            .'});'
            .'})();</script>';
    }

    /**
     * @param  array<string,  mixed>  $options
     */
    protected function renderBars(array $options): string
    {
        $distribution = $this->rating->getDistribution();
        $ratings = $this->rating->getRatings();

        $bars = $this->generateBarHTML($distribution, $ratings, $options);
        $summary = $this->renderSummary($options);

        $style = ! empty($options['inline_styles'])
            ? '<style>'.$this->generateBarCSS($options).'</style>'
            : '';

        return '<div '.$this->classes(
            'em-sim-rating',
            'em-sim-rating__bars',
            'em-sim-rating--'.$this->instanceSlug($options),
            'sim-rating-bars-container'
        ).' data-sim-rating-id="'.Escaper::attr($this->instanceId($options)).'">'
            .$style
            .$bars
            .$summary
            .'</div>';
    }

    /**
     * @param  array<string,  mixed>  $options
     */
    protected function generateBarCSS(array $options): string
    {
        $color = Escaper::color($options['color']);
        $height = Escaper::length($options['bar_height'], '20px');
        $spacing = Escaper::length($options['bar_spacing'], '8px');
        $borderRadius = Escaper::length($options['bar_border_radius'], '4px');

        $scope = '.em-sim-rating--'.$this->instanceSlug($options);

        $css = <<<CSS
            {$scope}{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;width:100%;max-width:600px;margin:0 auto;}
            {$scope} .em-sim-rating__bar{display:flex;align-items:center;margin-bottom:{$spacing};}
            {$scope} .em-sim-rating__bar-label{width:80px;font-size:0.9em;color:#555;}
            {$scope} .em-sim-rating__bar-track{flex-grow:1;background:#f5f5f5;border-radius:{$borderRadius};height:{$height};overflow:hidden;box-shadow:inset 0 1px 2px rgba(0,0,0,0.1);}
            {$scope} .em-sim-rating__bar-fill{height:100%;background:{$color};border-radius:{$borderRadius};transition:width 0.6s ease-out;box-shadow:0 1px 2px rgba(0,0,0,0.1);}
            {$scope} .em-sim-rating__bar-percent{width:50px;text-align:right;font-size:0.85em;color:#666;margin-left:10px;}
            {$scope} .em-sim-rating__bar-percent:empty{display:none;}
            {$scope} .em-sim-rating__summary{margin-top:15px;font-size:0.95em;color:#444;}
            CSS;

        return Escaper::css($css);
    }

    /**
     * @param  array<string,  float>  $distribution
     * @param  array<string,  int>  $ratings
     * @param  array<string,  mixed>  $options
     */
    protected function generateBarHTML(array $distribution, array $ratings, array $options): string
    {
        $labels = [
            'five_star' => '5-star',
            'four_star' => '4-star',
            'three_star' => '3-star',
            'two_star' => '2-star',
            'one_star' => '1-star',
        ];

        $showPercent = (bool) $options['bar_show_percentages'];
        $precision = (int) $options['bar_percentage_precision'];
        $suffix = Escaper::attr((string) $options['bar_percentage_suffix']);

        $html = '';
        foreach ($labels as $key => $label) {
            $percentage = isset($distribution[$key]) ? round((float) $distribution[$key], $precision) : 0.0;
            $count = isset($ratings[$key]) ? (int) $ratings[$key] : 0;
            $percentageText = number_format($percentage, $precision).$suffix;

            $percentDiv = $showPercent
                ? '<div '.$this->classes('em-sim-rating__bar-percent', 'sim-rating-bar-percent').'>'
                    .Escaper::attr($percentageText).'</div>'
                : '';

            $html .= '<div '.$this->classes('em-sim-rating__bar', 'sim-rating-bar').'>'
                .'<div '.$this->classes('em-sim-rating__bar-label', 'sim-rating-bar-label').'>'
                    .Escaper::attr($label).': '.Escaper::attr((string) $count).'</div>'
                .'<div '.$this->classes('em-sim-rating__bar-track', 'sim-rating-bar-bg').'>'
                    .'<div '.$this->classes('em-sim-rating__bar-fill', 'sim-rating-bar-fill')
                    .' style="width:'.Escaper::attr((string) $percentage).'%"></div></div>'
                .$percentDiv
                .'</div>';
        }

        return $html;
    }

    /**
     * @param  array<string,  mixed>  $options
     */
    protected function renderSummary(array $options): string
    {
        if (empty($options['show_summary'])) {
            return '';
        }

        $average = number_format($this->rating->getAverage(), 2);
        $total = (string) $this->rating->getTotal();

        return '<div '.$this->classes('em-sim-rating__summary', 'sim-rating-summary').'>Average: <strong>'
            .Escaper::attr($average).'</strong> from <strong>'.Escaper::attr($total).'</strong> total ratings</div>';
    }
}
