# Changelog

All changes to `emleons/sim-rating` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Namespaced BEM class system.** Every rendered element now carries a stable,
  vendor-prefixed BEM class (`em-sim-rating`, `em-sim-rating__star`,
  `em-sim-rating__bar-fill`, …) so you can style widgets from your own CSS file
  with **no raw-CSS injection risk**. The legacy `sim-rating*` classes are kept
  as secondary classes for backward compatibility.
- **`instance_id` option** — give a widget a stable id (e.g. `product-42`),
  surfaced as `data-sim-rating-id="product-42"` and an
  `em-sim-rating--product-42` scope class for per-widget targeting.
- **`inline_styles` option** — set to `false` to omit the per-instance inline
  `<style>` block (useful when you include the published CSS yourself).
- **`dist/sim-rating.css`** — a canonical stylesheet driven by CSS custom
  properties (`--em-sim-rating-color`, `--em-sim-rating-size`,
  `--em-sim-rating-bar-height`, …). Include it once and theme via variables or
  the BEM element classes.
- **Per-instance CSS scoping** — the default inline bar CSS is now scoped to the
  instance (`em-sim-rating--{id}`), fixing the previous issue where multiple bar
  widgets on one page shared/duplicated global styles.
- `Emleons\SimRating\Renderer\Concerns\InstanceAware` — shared helper trait for
  the class/instance utilities.
- New `tests/Renderer/StylingTest.php` covering class output, instance scoping,
  `inline_styles`, and `instance_id` sanitisation.

### Deprecated
- The legacy `sim-rating*` classes are deprecated in favour of the `em-sim-rating*`
  BEM classes and will be removed in a future major release.

## [2.2.0] - 2026-10-10

This release hardens the library for production use. All public constructors,
`render()` and getters remain backward compatible; the changes below are
internal improvements plus a small set of behaviour fixes.

### Added
- `declare(strict_types=1)` and strict typing across the entire `src/` tree.
- `Emleons\SimRating\Enum\StarKey` — a backed enum for the five rating buckets.
- `Emleons\SimRating\Enum\DisplayType` — a backed enum for the output formats.
- `Emleons\SimRating\Security\Escaper` — centralised sanitisation of every
  user-controllable value that flows into rendered markup.
- `Rating::render()` now accepts a `DisplayType` instance and an optional
  per-render `$options` override that does not mutate the instance.
- New `strict` option: when enabled, all five rating keys must be provided.
- New `empty_color` option to control the unfilled star colour.
- Accessibility improvements: `role="img"` + `aria-label` on every widget.
- Interactive star widgets now emit a dependency-free `sim-rating:change`
  custom event (no inline user data, no `eval`).
- Developer tooling: Laravel Pint, PHPStan (level 8), `composer` scripts
  (`lint`, `analyse`, `test`, `check`).
- `CHANGELOG.md`, `CONTRIBUTING.md`, `SECURITY.md`.

### Changed
- `composer.json`: requires `php >=8.2`, adds `autoload-dev`, `scripts`,
  `config` and `support`; fixes the author email typo; pins stable dev deps.
- Options are now defined once in `Rating::getDefaultOptions()` (renderers no
  longer redeclare them), preventing configuration drift.
- GitHub Actions now runs the test suite on PHP 8.2/8.3/8.4 with `pcov`
  coverage, plus a dedicated static-analysis job (Pint + PHPStan).
- `phpunit.xml` uses the modern `<source>` element (removes the deprecation
  warning) and fails on PHPUnit deprecations.

### Fixed
- **Security:** all colour, size and CSS option values are now validated and
  escaped, closing an HTML/attribute/CSS injection (XSS) vector.
- **Bug:** SVG gradient IDs are now unique per renderer instance, so multiple
  ratings on one page no longer collide (previously the first rating's colours
  could bleed into others).
- **Bug:** `SvgRenderer` now emits a real standalone `<svg>` instead of
  pretty-printed JSON. _This is a behaviour change for the `svg` format._
- `Rating` normalises partial rating arrays so `getAverage()`, `getTotal()`
  and `getDistribution()` always operate on all five buckets.

## [2.1.2] - 2025-01-01

### Fixed
- Undefined array key warning for the `show_summary` option.

[Unreleased]: https://github.com/emleonstz/sim-rating/compare/v2.2.0...HEAD
[2.2.0]: https://github.com/emleonstz/sim-rating/compare/v2.1.2...v2.2.0
[2.1.2]: https://github.com/emleonstz/sim-rating/releases/tag/v2.1.2
