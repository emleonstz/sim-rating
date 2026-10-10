# Contributing

Thanks for your interest in improving **sim-rating**! This document describes
how to set up the project locally and the quality bar every change must meet.

## Reporting bugs

Please open an issue with:
- A clear description of the problem.
- A minimal, runnable code sample.
- The PHP version and the exact `composer.json` constraint you are using.

For **security vulnerabilities, do not open a public issue** — see
[SECURITY.md](SECURITY.md).

## Local setup

```bash
git clone git@github.com:emleonstz/sim-rating.git
cd sim-rating
composer install
```

## The quality gate

Every pull request must pass the same checks that run in CI. Run them all with:

```bash
composer check
```

which is shorthand for:

```bash
# 1. Code style (Laravel Pint) — reports issues without changing files
composer lint

# 2. Static analysis (PHPStan, level 8)
composer analyse

# 3. Test suite (PHPUnit)
composer test
```

If `composer lint` fails, fix the style automatically with:

```bash
composer lint:fix
```

### Coverage

To run the suite with a coverage report you need a coverage driver
(`pcov` or `xdebug`):

```bash
composer test:coverage
```

## Coding standards

- `declare(strict_types=1);` is required at the top of every PHP file.
- Add parameter and return types everywhere; annotate iterable value types
  (e.g. `array<string, int>`) so PHPStan level 8 passes.
- Any value that ends up inside rendered HTML/SVG/CSS must go through the
  `Emleons\SimRating\Security\Escaper` helper.
- Keep the public API backward compatible wherever possible. If a change is
  breaking, call it out explicitly in the PR and add a CHANGELOG entry.
- Add or update tests for every behavioural change. Security fixes must
  include a regression test under `tests/Security/`.

## Commit messages

Use clear, conventional-style messages, e.g. `fix:`, `feat:`, `docs:`,
`refactor:`, `test:`.

## Releasing

1. Update `CHANGELOG.md` under a new version heading.
2. Tag the release (`git tag vX.Y.Z && git push --tags`).

## License

By contributing you agree that your contributions will be licensed under the
project's [MIT License](LICENSE).
