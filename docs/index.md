# sim-rating

sim-rating is a small PHP library that turns five star rating counts into a
clean average, a distribution, and a ready to display widget. It works with any
PHP project, including Laravel, Symfony, CodeIgniter, and plain PHP.

You give it how many people left one, two, three, four, and five stars. It gives
you back the maths and the markup, so you do not have to build a rating display
from scratch every time.

## What you get

- A weighted average, a total count, and a percentage distribution.
- Four output formats: HTML stars, HTML bars, a standalone SVG, and JSON.
- A namespaced set of CSS classes so you can style everything from your own
  stylesheet.
- Safe output. Every value that reaches the page is checked and escaped first.
- Full test coverage, static analysis, and a code style check.

## A quick look

```php
use Emleons\SimRating\Rating;

$rating = new Rating([
    'one_star'   => 10,
    'two_star'   => 20,
    'three_star' => 30,
    'four_star'  => 40,
    'five_star'  => 50,
]);

echo $rating->render();          // HTML stars
echo $rating->getAverage();      // 3.67
echo $rating->getTotal();        // 150
```

## Requirements

sim-rating needs PHP 8.3 or newer. It has no required runtime dependencies, so
installing it adds nothing else to your project.

## Where to go next

- New here? Start with [Installation](getting-started/installation.md), then the
  [Quickstart](getting-started/quickstart.md).
- Want to change how it looks? Read [Styling](guide/styling.md).
- Need the full list of settings? See the [Options reference](guide/options.md).
