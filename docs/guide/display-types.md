# Display types

sim-rating can present the same data in four ways. The numbers never change,
only the presentation. This page helps you pick the right one.

## HTML stars

```php
echo $rating->render('html');
```

This is the default. It prints a row of five stars with partial fill for
fractional averages, plus the average value and total count beside them.
Start here unless you have a reason to use something else.

Stars fit product cards, search results, review headers, and any place where
space is tight and readers expect the classic pattern.

Two settings change the structure most often:

```php
echo $rating->render('html', [
    'type'         => 'stars',
    'show_average' => true,
    'show_total'   => true,
]);
```

Set either one to `false` to hide that part.

## HTML bars

```php
echo $rating->render('html', ['type' => 'bars']);
```

Bars print five labelled rows, one per star level, with a track, a fill, and
the percentage. A summary line with the average and total can appear below.
Bars answer the question "how are the votes spread", where stars only answer
"what is the score".

Bars fit review sections, dashboards, and admin pages. They need more width
than stars, roughly a card or a column, to read well.

The bar specific options are listed in full in the
[Options reference](options.md). The most used ones:

```php
echo $rating->render('html', [
    'type'                   => 'bars',
    'bar_height'             => '20px',
    'bar_spacing'            => '8px',
    'bar_border_radius'      => '4px',
    'bar_show_counts'        => true,
    'bar_show_percentages'   => true,
    'bar_percentage_precision' => 1,
]);
```

## SVG

```php
echo $rating->render('svg');
```

SVG returns one standalone `svg` element with the stars drawn as shapes. It
needs no fonts, no icon set, and no stylesheet. The colour and size options
still apply.

Use SVG for HTML emails, PDF invoices, social preview images, feeds, and any
context where linked CSS may not load. If you need the numbers too, pair it
with a `json` call and place the text yourself.

## JSON

```php
echo $rating->render('json');
```

JSON returns `average`, `total`, and `distribution` as a JSON string. It is
meant for machines, not readers. Typical uses are JavaScript frontends that
draw their own widget, mobile apps fetching scores over HTTP, and cached API
responses.

```php
$data = json_decode($rating->render('json'), true);

echo $data['average']; // 3.67
echo $data['total'];   // 150
```

## Type safe selection

If you prefer objects over strings, the `DisplayType` enum offers the same
three formats with IDE support:

```php
use Emleons\SimRating\Enum\DisplayType;

echo $rating->render(DisplayType::Html);
echo $rating->render(DisplayType::Svg);
echo $rating->render(DisplayType::Json);
```

An unknown string throws an `InvalidArgumentException` with the value in the
message, so typos fail loudly instead of rendering the wrong thing.

## Per render overrides

The second argument to `render()` adjusts options for that call only. The
object itself is left unchanged, so later renders use the original settings.

```php
echo $rating->render('html', ['color' => '#e74c3c']); // red, this once
echo $rating->render(); // back to the instance colour
```
