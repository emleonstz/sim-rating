# Security Policy

## Supported Versions

Only the latest released minor version receives security fixes.

| Version | Supported          |
| ------- | ------------------ |
| 2.2.x   | :white_check_mark: |
| < 2.2   | :x:                |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

Instead, report them privately so we can address them before they are widely
known:

- Email: **emleons23@gmail.com**
- Or use GitHub's private vulnerability reporting
  ("Report a vulnerability" under the repository's **Security** tab).

Please include:
- A description of the vulnerability and its impact.
- Steps to reproduce, or a proof-of-concept.
- The affected version(s).
- Any suggested remediation, if you have one.

We will acknowledge your report within **72 hours**, provide an estimated
timeline for a fix, and credit you in the advisory (unless you prefer to
remain anonymous).

## Security Design Notes

sim-rating renders markup from caller-supplied data. The following guarantees
apply:

- **Output escaping.** Every value that flows into rendered HTML, SVG
  attributes, or inline CSS is passed through
  `Emleons\SimRating\Security\Escaper` before output.
- **Colour/length validation.** `color` and `size` (and bar sizing) options are
  validated against strict allow-lists; anything that does not match falls back
  to a safe default rather than being emitted verbatim.
- **No `eval` / no user data in scripts.** The optional interactive script is
  static; it reads values from `data-*` attributes at runtime and never
  interpolates user input into JavaScript.
- **Unique SVG IDs.** Each renderer instance generates unique gradient IDs so
  multiple widgets on a page cannot interfere with one another.

## Scope

The library performs no I/O, persistence, or network access. It is a pure
rendering/calculation utility; securing the data you pass to it (e.g. rating
counts sourced from a database or request) remains the responsibility of the
hosting application.
