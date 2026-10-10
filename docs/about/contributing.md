# Contributing

Thanks for your interest in sim-rating. This page covers the local setup and
the quality bar. The full text also lives in `CONTRIBUTING.md` in the
repository root.

## Setup

You need PHP 8.3 or newer and Composer.

```bash
git clone git@github.com:emleonstz/sim-rating.git
cd sim-rating
composer install
```

## The quality gate

One command runs everything CI runs:

```bash
composer check
```

That is shorthand for three steps:

```bash
composer lint     # code style check, Pint in test mode
composer analyse  # static analysis, PHPStan at level 8
composer test     # test suite, PHPUnit
```

Run `composer lint:fix` to repair style automatically. Run
`composer test:coverage` for a coverage report, which needs pcov or xdebug.

Every pull request must leave all three green.

## Code standards

  * Start every PHP file with `declare(strict_types=1);`.
  * Type all parameters and return values. Annotate array shapes so PHPStan
    stays quiet.
  * Send every value that reaches markup through the `Escaper` helper.
  * Keep the public API stable where possible. If a change breaks callers,
    say so plainly in the pull request and add a changelog entry.
  * Add or update tests with each behaviour change. Security fixes need a
    regression test under `tests/Security/`.

## Commits and releases

Write clear conventional messages such as `fix:`, `feat:`, `docs:`,
`refactor:`, or `test:`. To release, add a version section to
`CHANGELOG.md`, then tag with `git tag vX.Y.Z` and push the tag.

By contributing you agree your work ships under the MIT License in the
repository root.
