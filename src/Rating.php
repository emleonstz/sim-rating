<?php

declare(strict_types=1);

namespace Emleons\SimRating;

use Emleons\SimRating\Enum\DisplayType;
use Emleons\SimRating\Enum\StarKey;
use Emleons\SimRating\Interfaces\RendererInterface;
use Emleons\SimRating\Renderer\HtmlRenderer;
use Emleons\SimRating\Renderer\JsonRenderer;
use Emleons\SimRating\Renderer\SvgRenderer;

class Rating
{
    /**
     * The canonical, ordered set of rating buckets (one_star .. five_star).
     *
     * @var list<string>
     */
    public const STAR_KEYS = ['one_star', 'two_star', 'three_star', 'four_star', 'five_star'];

    /** @var array<string, int> */
    protected array $ratings;

    /** @var array<string, mixed> */
    protected array $options;

    /**
     * @param  array<string, int|float|string>  $ratings
     * @param  array<string, mixed>  $options
     */
    public function __construct(array $ratings = [], array $options = [])
    {
        // Defaults are resolved first so validation can honour the "strict" flag.
        $this->options = $this->getDefaultOptions();
        $this->options = array_merge($this->options, $options);

        $this->setRatings($ratings);
    }

    /**
     * Replace the rating counts.
     *
     * @param  array<string, int|float|string>  $ratings
     */
    public function setRatings(array $ratings): self
    {
        $this->validateRatings($ratings, (bool) ($this->options['strict'] ?? false));
        $this->ratings = $this->normalizeRatings($ratings);

        return $this;
    }

    /**
     * Merge additional options into the current configuration.
     *
     * @param  array<string, mixed>  $options
     */
    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * The weighted average rating, rounded to two decimal places.
     */
    public function getAverage(): float
    {
        $sum = 0;
        $total = 0;

        foreach ($this->ratings as $key => $count) {
            $sum += StarKey::fromKey($key)->value * $count;
            $total += $count;
        }

        return $total > 0 ? round($sum / $total, 2) : 0.0;
    }

    /**
     * Total number of ratings across all buckets.
     */
    public function getTotal(): int
    {
        return (int) array_sum($this->ratings);
    }

    /**
     * The percentage distribution of ratings per bucket.
     *
     * @return array<string, float>
     */
    public function getDistribution(): array
    {
        $total = $this->getTotal();
        $distribution = [];

        foreach ($this->ratings as $key => $count) {
            $distribution[$key] = $total > 0 ? ($count / $total) * 100 : 0.0;
        }

        return $distribution;
    }

    /**
     * Render the rating in the requested format.
     *
     * @param  array<string, mixed>  $options  Optional per-render option overrides.
     */
    public function render(string|DisplayType $type = 'html', array $options = []): string
    {
        $displayType = $type instanceof DisplayType ? $type : DisplayType::fromString($type);

        // Apply per-render overrides without mutating the instance.
        $rating = $options === [] ? $this : (clone $this)->setOptions($options);

        return $rating->createRenderer($displayType)->render();
    }

    /**
     * Build the renderer responsible for a given display type.
     */
    protected function createRenderer(DisplayType $type): RendererInterface
    {
        return match ($type) {
            DisplayType::Html => new HtmlRenderer($this),
            DisplayType::Json => new JsonRenderer($this),
            DisplayType::Svg => new SvgRenderer($this),
        };
    }

    /**
     * The full, merged option set including all renderer defaults.
     *
     * @return array<string, mixed>
     */
    public function getDefaultOptions(): array
    {
        return [
            // Core display
            'type' => 'stars',
            'color' => '#ffc107',
            'empty_color' => '#ddd',
            'size' => '1em',
            // Toggles
            'show_total' => true,
            'show_average' => true,
            'show_summary' => true,
            'interactive' => false,
            'readonly' => true,
            // Styling
            'instance_id' => null,
            'inline_styles' => true,
            // Validation
            'strict' => false,
            // Bar-specific options
            'bar_height' => '20px',
            'bar_spacing' => '8px',
            'bar_border_radius' => '4px',
            'bar_show_counts' => true,
            'bar_show_percentages' => true,
            'bar_percentage_precision' => 1,
            'bar_percentage_suffix' => '%',
            'bar_animate' => true,
        ];
    }

    /**
     * Ensure every supplied key is valid, values are non-negative integers and
     * (optionally) that all five buckets are present.
     *
     * @param  array<string, int|float|string>  $ratings
     */
    protected function validateRatings(array $ratings, bool $strict = false): void
    {
        $validKeys = self::STAR_KEYS;

        foreach ($ratings as $key => $value) {
            if (! in_array($key, $validKeys, true)) {
                throw new \InvalidArgumentException("Invalid rating key: {$key}");
            }

            // Accept integers only; tolerate whole-number floats (e.g. 5.0 from JSON/DB).
            if (is_string($value) || (is_float($value) && floor($value) !== $value) || ! is_numeric($value)) {
                throw new \InvalidArgumentException("Rating count must be a non-negative integer for {$key}");
            }

            if ((int) $value < 0) {
                throw new \InvalidArgumentException("Rating count must be a non-negative integer for {$key}");
            }
        }

        if ($strict) {
            foreach ($validKeys as $key) {
                if (! array_key_exists($key, $ratings)) {
                    throw new \InvalidArgumentException("Missing required rating key: {$key}");
                }
            }
        }
    }

    /**
     * Normalise the incoming array so every bucket is present and integer.
     *
     * @param  array<string, int|float|string>  $ratings
     * @return array<string, int>
     */
    protected function normalizeRatings(array $ratings): array
    {
        $normalized = [];

        foreach (self::STAR_KEYS as $key) {
            $normalized[$key] = (int) ($ratings[$key] ?? 0);
        }

        return $normalized;
    }

    /**
     * @return array<string, int>
     */
    public function getRatings(): array
    {
        return $this->ratings;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
