# Sim-Rating - A Simple 5-Star Rating System for PHP

[![Latest Version](https://img.shields.io/packagist/v/emleons/sim-rating.svg)](https://packagist.org/packages/emleons/sim-rating)
[![License](https://img.shields.io/github/license/emleonstz/sim-rating.svg)](https://github.com/emleonstz/sim-rating/blob/main/LICENSE)

Sim-Rating is a lightweight,PHP library for displaying and calculating 5-star ratings. It supports multiple display formats (stars, bars, JSON) and is highly customizable and works with any php frame work.

## Features

- 🎯 Works with PHP Framework and PHP plain implementation
- ⭐ Multiple output formats (HTML, JSON, SVG)
- 🎨 Customizable colors, sizes and styles
- 📊 Calculate averages, totals and distributions
- 📱 Responsive and mobile-friendly
- ✅ 100% unit tested

## Installation

Install via Composer:

```bash
composer require emleons/sim-rating
```

## Basic Usage

```php
use Emleons\SimRating\Rating;

// Make sure you are having an array with the given keys to Initialize with rating counts
$ratings = [
    'one_star' => 10,
    'two_star' => 20,
    'three_star' => 30,
    'four_star' => 40,
    'five_star' => 50
];

$rating = new Rating($ratings);

// Render as HTML stars
echo $rating->render('html');

// Get as JSON
echo $rating->render('json');

// Get calculated values
$average = $rating->getAverage(); // 3.67
$total = $rating->getTotal();     // 150
```

## Customization

```php
// With custom options
$rating = new Rating($ratings, [
    'type' => 'stars',
    'color' => '#ff6b35',
    'size' => '24px',
    'show_total' => false,
    'interactive' => true
]);

// Change options later
$rating->setOptions([
    'color' => '#4285f4'
]);
```

## Display Types

### Stars
```php
echo $rating->render('html'); // Default star display
```

### Bars
```php
echo $rating->render('html', ['type' => 'bars']);
```

### JSON Output
```php
echo $rating->render('json');
// {
//   "average": 3.67,
//   "total": 150,
//   "distribution": {
//     "one_star": 6.67,
//     "two_star": 13.33,
//     ...
//   }
// }
```

## Advanced Usage

### Custom Templates
```php
// Create custom template at path/to/template.php
echo $rating->render('html', [
    'template' => 'path/to/template.php'
]);
```

### Using in Frameworks

**Laravel:**
```php
// In controller
public function show(Product $product)
{
    return view('products.show', [
        'rating' => new \SimRating\Rating($product->ratings)
    ]);
}

// In Blade
{!! $rating->render() !!}
```

**Symfony:**
```php
// In controller
public function show(Product $product): Response
{
    return $this->render('product/show.html.twig', [
        'rating' => new \SimRating\Rating($product->getRatings())
    ]);
}

// In Twig
{{ rating.render()|raw }}
```

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit
```

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.

## License

MIT License. See [LICENSE](LICENSE) for more information.