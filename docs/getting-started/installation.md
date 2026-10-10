# Installation

## Requirements

sim-rating needs **PHP 8.3 or newer**. It has no required runtime dependencies.

## Install with Composer

From your project root, run:

```bash
composer require emleons/sim-rating
```

Composer downloads the library and sets up autoloading. After that, the classes
are ready to use.

## Check it works

Create a small PHP file to confirm everything is wired up:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Emleons\SimRating\Rating;

$rating = new Rating(['five_star' => 5]);

echo $rating->getAverage(); // 5
```

Run it with `php yourfile.php`. If you see `5`, the install worked.

## Using it inside a framework

sim-rating is a normal PHP library, so it drops into any framework. A few notes:

- **Laravel:** the package is autoloaded by Composer. In a Blade view you can
  build the object in your controller and pass it to the view, then print it
  with `{!! $rating->render() !!}`.
- **Symfony:** create the object in your controller and pass it to Twig, then
  print with `{{ rating.render()|raw }}`.
- **CodeIgniter 4:** create the object in your controller and pass the rendered
  string to your view.

If your project already uses Composer autoloading, there is nothing extra to
configure.
