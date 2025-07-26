<?php

namespace Emleons\SimRating\Renderer;

use Emleons\SimRating\Rating;
use Emleons\SimRating\Interfaces\RendererInterface;

class HtmlRenderer implements RendererInterface
{
    protected Rating $rating;

    public function __construct(Rating $rating)
    {
        $this->rating = $rating;
    }

    public function render(): string
    {
        $options = $this->rating->getOptions();

        return match ($options['type']) {
            'bars' => $this->renderBars(),
            default => $this->renderDefault()
        };
    }

    protected function renderDefault(): string
    {
        $options = $this->rating->getOptions();
        $average = $this->rating->getAverage();
        $total = $this->rating->getTotal();

        ob_start();
?>
        <div class="sim-rating" data-rating-average="<?= $average ?>">
            <?= $this->renderStars($average) ?>
            <?php if ($options['show_average']): ?>
                <span class="sim-rating-average"><?= number_format($average, 1) ?></span>
            <?php endif; ?>
            <?php if ($options['show_total']): ?>
                <span class="sim-rating-total">(<?= $total ?> ratings)</span>
            <?php endif; ?>
        </div>
        <?php if ($options['interactive']): ?>
            <script>
                // Interactive JS would go here
            </script>
<?php endif;

        return ob_get_clean();
    }

    protected function renderStar(string $type, int $position): string
    {
        $options = $this->rating->getOptions();
        $color = $type === 'empty' ? '#ddd' : $options['color'];
        $size = $options['size'];

        $fill = $type === 'empty' ? 'none' : $color;
        $stroke = $color;
        $percentage = $type === 'half' ? '50%' : '100%';

        return <<<SVG
    <svg width="$size" height="$size" viewBox="0 0 24 24" data-rating-value="$position">
        <defs>
            <linearGradient id="grad$position">
                <stop offset="$percentage" stop-color="$color"/>
                <stop offset="$percentage" stop-color="transparent"/>
            </linearGradient>
        </defs>
        <path 
            fill="url(#grad$position)" 
            stroke="$stroke" 
            stroke-width="1"
            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
        />
    </svg>
SVG;
    }

    protected function renderStars(float $average): string
    {
        $options = $this->rating->getOptions();
        $fullStars = floor($average);
        $hasHalfStar = ($average - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
        $output = '';

        // Full stars
        for ($i = 0; $i < $fullStars; $i++) {
            $output .= $this->renderStar('full', $i + 1);
        }

        // Half star
        if ($hasHalfStar) {
            $output .= $this->renderStar('half', $fullStars + 1);
        }

        // Empty stars
        for ($i = 0; $i < $emptyStars; $i++) {
            $output .= $this->renderStar('empty', $fullStars + $hasHalfStar + $i + 1);
        }

        return $output;
    }

    protected function renderSvgStar(string $type, string $color, string $size, int $position): string
    {
        $fill = $type === 'empty' ? 'none' : $color;
        $stroke = $color;
        $strokeWidth = 1;
        $percentage = $type === 'half' ? '50%' : '100%';

        return <<<SVG
    <svg width="$size" height="$size" viewBox="0 0 24 24" data-rating-value="$position">
        <defs>
            <linearGradient id="grad{$position}">
                <stop offset="$percentage" stop-color="$color"/>
                <stop offset="$percentage" stop-color="transparent"/>
            </linearGradient>
        </defs>
        <path 
            fill="url(#grad{$position})" 
            stroke="$stroke" 
            stroke-width="$strokeWidth"
            d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
        />
    </svg>
SVG;
    }

    protected function renderCustomTemplate(string $templatePath): string
    {
        extract([
            'rating' => $this->rating,
            'average' => $this->rating->getAverage(),
            'total' => $this->rating->getTotal(),
            'options' => $this->rating->getOptions(),
            'distribution' => $this->rating->getDistribution()
        ]);

        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
    protected function renderBars(): string
    {
        $options = $this->rating->getOptions();
        $distribution = $this->rating->getDistribution();
        $ratings = $this->rating->getRatings();

        $css = $this->generateBarCSS($options);
        $bars = $this->generateBarHTML($distribution, $ratings, $options);

        return <<<HTML
        <div class="sim-rating-bars-container">
            <style>{$css}</style>
            {$bars}
            {$this->renderSummary()}
        </div>
        HTML;
    }

    protected function generateBarCSS(array $options): string
    {
        $color = $options['color'] ?? '#4a90e2';
        $height = $options['bar_height'] ?? '20px';
        $spacing = $options['bar_spacing'] ?? '8px';
        $borderRadius = $options['bar_border_radius'] ?? '4px';

        return <<<CSS
        .sim-rating-bars-container {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        .sim-rating-bar {
            display: flex;
            align-items: center;
            margin-bottom: {$spacing};
        }
        .sim-rating-bar-label {
            width: 80px;
            font-size: 0.9em;
            color: #555;
        }
        .sim-rating-bar-bg {
            flex-grow: 1;
            background: #f5f5f5;
            border-radius: {$borderRadius};
            height: {$height};
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }
        .sim-rating-bar-fill {
            height: 100%;
            background: {$color};
            border-radius: {$borderRadius};
            transition: width 0.6s ease-out;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .sim-rating-bar-percent {
            width: 50px;
            text-align: right;
            font-size: 0.85em;
            color: #666;
            margin-left: 10px;
        }
        .sim-rating-summary {
            margin-top: 15px;
            font-size: 0.95em;
            color: #444;
        }
        CSS;
    }

    protected function generateBarHTML(array $distribution, array $ratings, array $options): string
    {
        $html = '';
        $starLabels = [
            'five_star' => '5-star',
            'four_star' => '4-star',
            'three_star' => '3-star',
            'two_star' => '2-star',
            'one_star' => '1-star'
        ];

        foreach ($starLabels as $key => $label) {
            $percentage = $distribution[$key] ?? 0;
            $count = $ratings[$key] ?? 0;

            $html .= <<<HTML
            <div class="sim-rating-bar">
                <div class="sim-rating-bar-label">
                    {$label}: {$count}
                </div>
                <div class="sim-rating-bar-bg">
                    <div class="sim-rating-bar-fill" style="width: {$percentage}%"></div>
                </div>
                <div class="sim-rating-bar-percent">
                    {$percentage}%
                </div>
            </div>
            HTML;
        }

        return $html;
    }

    protected function renderSummary(): string
    {
        if (!$this->rating->getOptions()['show_summary']) {
            return '';
        }

        $average = $this->rating->getAverage();
        $total = $this->rating->getTotal();

        return <<<HTML
        <div class="sim-rating-summary">
            Average: <strong>{$average}</strong> from <strong>{$total}</strong> total ratings
        </div>
        HTML;
    }
}
