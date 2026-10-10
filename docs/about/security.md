# Security

Rating widgets sit on public pages and often display data. Always validate input using appropriate filters and escape output before rendering it on a page. Never allow unchecked user input to reach the browser.

## What is escaped

All rendered output passes through the `Escaper` helper before it reaches
the page. Colours, sizes, spacing values, labels, counts, percentages, ids,
and text all go through validation or HTML escaping first. A hostile string
such as a script tag in a colour option is neutralised or replaced with the
safe default. It is never printed as given.

## What is validated

Options with a fixed shape use allow lists. `color` and `empty_color`
accept hex codes, rgb functions, and plain colour names. `size` and the bar
dimensions accept numeric CSS lengths with safe units. The `instance_id`
used for per widget styling is slugified for class use and escaped for
attribute use. Anything that fails validation falls back to the default for
that option.

## No raw CSS option

The library offers no setting that prints caller supplied CSS verbatim.
Allowing that would punch a hole straight through the escaping described
above. Customise appearance through the documented classes, theme variables,
and validated options instead. The [Styling](../guide/styling.md) page shows
the full set.

## Scripts without user data

The optional interactive stars emit a `sim-rating:change` browser event so
your JavaScript can react to clicks. That script block is fully static. It
carries no user values inside it and reads state from data attributes at
runtime. There is no `eval` and no string built from user input anywhere in
the library.

## Instance isolation

Each render gets its own identity. SVG gradient ids are unique per instance,
and the default bar stylesheet is scoped to the instance id. One widget
cannot restyle or relabel another on the same page.

## What stays your job

The library does no storage and no network calls. It cannot know where your
counts came from. Keep validating and authorising the data on your side,
especially if counts can be submitted by visitors. Escape on output in the
rest of your templates as well, and prefer `{!! !!}` or `|raw` only for the
already safe render string.

## Reporting a problem

If you find a bypass or a suspicious output, please report it privately by
email to **emleons23@gmail.com** instead of opening a public issue. Include
the version, a short reproduction, and the output you saw. Reports are
acknowledged within 72 hours. Full details live in `SECURITY.md` in the
repository root.
