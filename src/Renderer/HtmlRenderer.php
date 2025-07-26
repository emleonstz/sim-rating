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

        if ($options['template'] && is_file($options['template'])) {
            return $this->renderCustomTemplate($options['template']);
        }

        return $this->renderDefault();
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

        // Use custom color for empty stars if specified
        $emptyColor = $options['empty_color'] ?? '#ddd';
        $color = $type === 'empty' ? $emptyColor : $options['color'];
        $size = $options['size'];

        if ($options['style'] === 'svg') {
            return $this->renderSvgStar($type, $color, $size, $position);
        }

        // Default CSS-based star
        $class = $type === 'half' ? 'fa-star-half-alt' : 'fa-star';
        $prefix = $type === 'empty' ? 'far' : 'fas';

        return sprintf(
            '<i class="%s %s" style="color: %s; font-size: %s;" data-rating-value="%d"></i>',
            $prefix,
            $class,
            $color,
            $size,
            $position
        );
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
        // SVG star implementation would go here
        return '';
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
}
