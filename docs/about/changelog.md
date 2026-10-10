# Changelog

This page summarises recent changes. The complete history lives in
`CHANGELOG.md` in the repository root, kept in the Keep a Changelog format
with Semantic Versioning.

## Unreleased

  * Namespaced BEM classes on every element (`em-sim-rating` family), with
    the legacy classes kept as secondary output for compatibility.
  * New `instance_id` option for stable per widget targeting.
  * New `inline_styles` option to omit the default style block when you ship
    `dist/sim-rating.css` yourself.
  * New canonical stylesheet at `dist/sim-rating.css`, driven by theme
    variables.
  * Default bar CSS is now scoped per instance, so multiple widgets on one
    page stay independent.

## Version 2.2.0

A production hardening release. Public constructors, `render()`, and getters
stayed compatible.

  * Strict typing across all of `src`, plus `StarKey` and `DisplayType`
    enums and a central `Escaper` for output safety.
  * Per render option overrides that leave the instance unchanged.
  * New `strict` and `empty_color` options, and accessible markup with roles
    and labels.
  * Tooling: Pint, PHPStan at level 8, and one command quality gate.
  * Fixed: colour and size injection closed, unique SVG ids per instance,
    and the SVG renderer now returns real SVG instead of JSON.

## Version 2.1.2

  * Fixed an undefined array key warning for the `show_summary` option.
