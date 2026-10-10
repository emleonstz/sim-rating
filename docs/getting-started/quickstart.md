# Quickstart

This documentation provides a comprehensive guide to implementing the rating widget, enabling you to achieve a functional integration quickly.

## The basic pattern

Every example follows the same two steps. First you create a `Rating`
object with your counts. Then you print something from it.

```php
use Emleons\SimRating\Rating;

$rating = new Rating([
    'one_star'   => 10,
    'two_star'   => 20,
    'three_star' => 30,
    'four_star'  => 40,
    'five_star'  => 50,
]);
```

Missing keys default to zero, so `new Rating(['five_star' => 5])` is valid.
If you want the library to insist on all five keys, pass `'strict' => true`
in the options. You can read more about that in the
[Options reference](../guide/options.md).

## Stars

Stars are the default output. This is the right choice for product pages,
review summaries, and anywhere people expect the familiar row of stars.

```php
echo $rating->render();
```

To hide the average or the total count:

```php
echo $rating->render('html', [
    'show_average' => false,
    'show_total'   => true,
]);
```

To change the look:

```php
echo $rating->render('html', [
    'color'       => '#e74c3c',
    'empty_color' => '#eee',
    'size'        => '1.5rem',
]);
```

## Bars

Bars show the full distribution, one row per star level. They suit analytics
pages, admin screens, and detailed review sections.

```php
echo $rating->render('html', ['type' => 'bars']);
```

Bar specific settings live alongside the normal ones:

```php
echo $rating->render('html', [
    'type'                 => 'bars',
    'color'                => '#4a90e2',
    'bar_height'           => '25px',
    'bar_show_percentages' => true,
    'show_summary'         => true,
]);
```

## SVG

The SVG format returns a standalone picture with no extra markup around it.
Use it in emails, PDFs, image pipelines, or anywhere you need one portable
graphic.

```php
echo $rating->render('svg');
```

## JSON

JSON returns the numbers without any markup. Use it when your frontend
renders the widget itself, or when you are building an API response.

```php
echo $rating->render('json');
```

The decoded shape looks like this:

```json
{
    "average": 3.67,
    "total": 150,
    "distribution": {
        "one_star": 6.67,
        "two_star": 13.33,
        "three_star": 20.0,
        "four_star": 26.67,
        "five_star": 33.33
    }
}
```

## Reading the numbers directly

You can also skip rendering and use the maths in your own code:

```php
$rating->getAverage();      // 3.67
$rating->getTotal();        // 150
$rating->getDistribution(); // array of five percentages
$rating->getRatings();      // the normalised counts
```

## What's next

  * [Display types](../guide/display-types.md) explains each format in depth.
  * [Styling](../guide/styling.md) shows how to match the widget to your site.
  * [Options reference](../guide/options.md) lists every setting.
