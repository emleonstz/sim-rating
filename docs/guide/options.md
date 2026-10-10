# Options reference

Every setting below can be passed to the constructor or changed later with
`setOptions()`. The `render()` method also accepts overrides for a single
call without changing the object.

```php
use Emleons\SimRating\Rating;

$rating = new Rating($counts, [
    'color' => '#e74c3c',
    'size'  => '1.5rem',
]);

$rating->setOptions(['show_total' => false]);

echo $rating->render('html', ['color' => '#2ecc71']); // this call only
```

## Core display

### type

Which layout the HTML renderer prints. Allowed values are `stars` and
`bars`. Default is `stars`.

```php
['type' => 'bars']
```

### color

The fill colour for stars and bars. Accepts hex, rgb, and plain colour
names. Anything outside the allow list falls back to the default instead of
being printed. Default is `#ffc107`.

```php
['color' => '#e74c3c']
```

### empty_color

The colour of the unfilled part of a star. Validated the same way as
`color`. Default is `#ddd`.

```php
['empty_color' => '#eee']
```

### size

The star size, as a CSS length such as `1em`, `24px`, or `1.5rem`. Values
outside the allow list fall back to the default. Default is `1em`.

```php
['size' => '1.5rem']
```

## Visibility toggles

### show_average

Show the numeric average beside the stars. Default is `true`.

### show_total

Show the total vote count beside the stars. Default is `true`.

### show_summary

Show the summary line under the bars layout. Default is `true`.

```php
['show_average' => false, 'show_total' => true, 'show_summary' => true]
```

## Behaviour

### interactive

Render clickable stars that emit a `sim-rating:change` browser event with
the chosen value. The script is static and reads from data attributes, so no
user input ever lands inside JavaScript. Default is `false`.

### readonly

Marks the widget as readonly for assistive tech. Default is `true`.

## Styling hooks

### instance_id

A stable identity for one widget placement, for example `product-42`. It is
printed as a `data-sim-rating-id` attribute and a scope class, and unsafe
characters are stripped. When left as `null`, a unique id is generated per
render. Default is `null`.

### inline_styles

When `true`, the bars layout prints a small scoped `style` block with its
defaults. Set to `false` when you include `dist/sim-rating.css` yourself.
Default is `true`.

## Validation

### strict

When `true`, constructing or updating the object throws unless all five
rating keys are present. When `false`, missing keys are treated as zero.
Default is `false`.

```php
new Rating($data, ['strict' => true]);
```

## Bar layout

These only affect `type => bars`.

### bar_height

Thickness of each bar track. Default is `20px`.

### bar_spacing

Vertical space between bar rows. Default is `8px`.

### bar_border_radius

Corner rounding of tracks and fills. Default is `4px`.

### bar_show_counts

Show the raw vote count on each row. Default is `true`.

### bar_show_percentages

Show the percentage on each row. Default is `true`.

### bar_percentage_precision

How many decimals the percentages print with. Default is `1`.

### bar_percentage_suffix

Text printed right after each percentage value. Default is `%`. Set to an
empty string to drop it.

### bar_animate

Animate the fill width when the page loads. Default is `true`.

```php
[
    'type'                     => 'bars',
    'bar_height'               => '25px',
    'bar_spacing'              => '8px',
    'bar_border_radius'        => '4px',
    'bar_show_counts'          => true,
    'bar_show_percentages'     => true,
    'bar_percentage_precision' => 1,
    'bar_percentage_suffix'    => '%',
    'bar_animate'              => true,
]
```
