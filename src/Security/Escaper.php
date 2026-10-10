<?php

declare(strict_types=1);

namespace Emleons\SimRating\Security;

/**
 * Centralised sanitisation helpers for all user-controllable values that end
 * up inside rendered markup.
 *
 * The guiding rule: never trust options. Every value that flows into HTML
 * attributes, SVG attributes or inline CSS is passed through one of these
 * helpers first.
 */
final class Escaper
{
    /**
     * Colour values considered safe to emit verbatim. Covers:
     *   - #rgb / #rrggbb / #rrggbbaa
     *   - rgb() / rgba() / hsl() / hsla() functional notation
     *   - the CSS named colours + "transparent" / "currentcolor"
     */
    private const COLOR_PATTERN = '/^(#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})|'
        .'(?:rgb|rgba|hsl|hsla)\(\s*[0-9a-z%.,\s\/+-]*\)|'
        .'(?:aliceblue|antiquewhite|aqua|aquamarine|azure|beige|bisque|black|blanchedalmond|blue|blueviolet|brown|burlywood|cadetblue|chartreuse|chocolate|coral|cornflowerblue|cornsilk|crimson|cyan|darkblue|darkcyan|darkgoldenrod|darkgray|darkgreen|darkgrey|darkkhaki|darkmagenta|darkolivegreen|darkorange|darkorchid|darkred|darksalmon|darkseagreen|darkslateblue|darkslategray|darkslategrey|darkturquoise|darkviolet|deeppink|deepskyblue|dimgray|dimgrey|dodgerblue|firebrick|floralwhite|forestgreen|fuchsia|gainsboro|ghostwhite|gold|goldenrod|gray|green|greenyellow|grey|honeydew|hotpink|indianred|indigo|ivory|khaki|lavender|lavenderblush|lawngreen|lemonchiffon|lightblue|lightcoral|lightcyan|lightgoldenrodyellow|lightgray|lightgreen|lightgrey|lightpink|lightsalmon|lightseagreen|lightskyblue|lightslategray|lightslategrey|lightsteelblue|lightyellow|lime|limegreen|linen|magenta|maroon|mediumaquamarine|mediumblue|mediumorchid|mediumpurple|mediumseagreen|mediumslateblue|mediumspringgreen|mediumturquoise|mediumvioletred|midnightblue|mintcream|mistyrose|moccasin|navajowhite|navy|oldlace|olive|olivedrab|orange|orangered|orchid|palegoldenrod|palegreen|paleturquoise|palevioletred|papayawhip|peachpuff|peru|pink|plum|powderblue|purple|rebeccapurple|red|rosybrown|royalblue|saddlebrown|salmon|sandybrown|seagreen|seashell|sienna|silver|skyblue|slateblue|slategray|slategrey|snow|springgreen|steelblue|tan|teal|thistle|tomato|turquoise|violet|wheat|white|whitesmoke|yellow|yellowgreen|transparent|currentcolor))$/i';

    /**
     * CSS length values: a number followed by a known unit, a bare number,
     * or "auto"/"0".
     */
    private const LENGTH_PATTERN = '/^-?\d+(\.\d+)?(px|em|rem|ex|ch|%|vh|vw|vmin|vmax|pt|pc|cm|mm|in)?$|^(auto|inherit|initial|0)$/i';

    private function __construct() {}

    /**
     * Escape a value for safe use inside an HTML/SVG attribute or text node.
     */
    public static function attr(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Validate a CSS colour, returning a guaranteed-safe value.
     *
     * If the supplied value does not match a safe colour format the provided
     * fallback is returned instead (never the raw input).
     */
    public static function color(mixed $value, string $fallback = '#ffc107'): string
    {
        $value = is_string($value) ? trim($value) : '';

        return preg_match(self::COLOR_PATTERN, $value) === 1 ? $value : $fallback;
    }

    /**
     * Validate a CSS length/size value (used for sizes, bar heights, radii...).
     */
    public static function length(mixed $value, string $fallback = '1em'): string
    {
        $value = is_string($value) ? trim($value) : '';

        return preg_match(self::LENGTH_PATTERN, $value) === 1 ? $value : $fallback;
    }

    /**
     * Escape an arbitrary string for embedding inside a <style> block.
     *
     * Strips characters that could terminate the style element or inject new
     * declarations ("<", ">", "&" and quote/brace-breaking sequences).
     */
    public static function css(mixed $value): string
    {
        $value = (string) $value;
        $value = str_replace(['<', '>', '&'], '', $value);

        return str_replace(['"', "'", '\\'], '', $value);
    }
}
