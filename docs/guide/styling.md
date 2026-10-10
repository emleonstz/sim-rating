# Styling

The widget ships with sensible default looks, but real projects need their
own identity. sim-rating gives you three clean ways to customise appearance,
all from your own stylesheet. There is deliberately no option that accepts
raw CSS text, and the [Security](../about/security.md) page explains why.

## The class system

Every rendered element carries two classes. The first is the namespaced BEM
class. That is the one to target. The second is the legacy class, kept so
older stylesheets keep working.

  * Root: `em-sim-rating` (legacy: `sim-rating`)
  * Stars wrapper: `em-sim-rating__stars`
  * Each star: `em-sim-rating__star` (legacy: `sim-rating-star`)
  * Average value: `em-sim-rating__average` (legacy: `sim-rating-average`)
  * Total count: `em-sim-rating__total` (legacy: `sim-rating-total`)
  * Bars root: `em-sim-rating__bars` (legacy: `sim-rating-bars-container`)
  * One bar row: `em-sim-rating__bar` (legacy: `sim-rating-bar`)
  * Bar label: `em-sim-rating__bar-label` (legacy: `sim-rating-bar-label`)
  * Bar track: `em-sim-rating__bar-track` (legacy: `sim-rating-bar-bg`)
  * Bar fill: `em-sim-rating__bar-fill` (legacy: `sim-rating-bar-fill`)
  * Bar percentage: `em-sim-rating__bar-percent` (legacy: `sim-rating-bar-percent`)
  * Summary line: `em-sim-rating__summary` (legacy: `sim-rating-summary`)
  * SVG root: `em-sim-rating__svg` (legacy: `sim-rating-svg`)

A simple override looks like this:

```css
.em-sim-rating__average {
    font-weight: 700;
}

.em-sim-rating__bar-fill {
    border-radius: 999px;
}
```

The legacy classes are deprecated and will be removed in a future major
release, so write all new CSS against the `em-sim-rating` names.

## The published stylesheet

The file `dist/sim-rating.css` holds the canonical styles. Include it once
in your page, then render with inline styles switched off to avoid a
repeated `style` block per widget:

```php
echo $rating->render('html', ['inline_styles' => false]);
```

```html
<link rel="stylesheet" href="/vendor/emleons/sim-rating/dist/sim-rating.css">
```

Adjust the path to match where Composer installed the package in your
project, or copy the file into your own assets folder.

## Theme variables

The published stylesheet is driven by custom properties. Override any of
them on the widget root to retheme without writing new rules:

```css
.em-sim-rating {
    --em-sim-rating-color: #ffc107;
    --em-sim-rating-empty-color: #ddd;
    --em-sim-rating-size: 1em;
    --em-sim-rating-gap: 2px;
    --em-sim-rating-font: system stack;
    --em-sim-rating-bar-height: 20px;
    --em-sim-rating-bar-spacing: 8px;
    --em-sim-rating-bar-radius: 4px;
    --em-sim-rating-bar-track: #f5f5f5;
}
```

The quick PHP side equivalents are the `color`, `empty_color`, `size`,
`bar_height`, `bar_spacing`, and `bar_border_radius` options. Use the
options for one widget and the variables for a whole site.

## Targeting one widget

Pass `instance_id` to give a widget a stable identity. It appears as a
`data-sim-rating-id` attribute and as a scope class, so you can style one
placement without touching the others:

```php
echo $rating->render('html', ['instance_id' => 'product-42']);
```

```css
.em-sim-rating[data-sim-rating-id="product-42"] {
    --em-sim-rating-color: #e74c3c;
}
```

Without an explicit id, each render generates a unique one, and the default
inline bar CSS is scoped to it. Multiple widgets on one page never share
styles by accident.

## Quick recipes

Bigger stars with a warm colour:

```php
echo $rating->render('html', [
    'color' => '#f39c12',
    'size'  => '1.5rem',
]);
```

Slim bars with counts hidden:

```php
echo $rating->render('html', [
    'type'            => 'bars',
    'bar_height'      => '10px',
    'bar_show_counts' => false,
]);
```

Dark track for a dark theme, from your stylesheet:

```css
.dark-theme .em-sim-rating {
    --em-sim-rating-bar-track: #333;
    --em-sim-rating-empty-color: #555;
}
```
