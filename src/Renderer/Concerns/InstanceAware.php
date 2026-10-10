<?php

declare(strict_types=1);

namespace Emleons\SimRating\Renderer\Concerns;

/**
 * Shared helpers for renderers that expose a namespaced (BEM) class system and
 * a per-instance identity for styling hooks.
 *
 * Expects the using class to define a unique `$uid` string property.
 */
trait InstanceAware
{
    /**
     * Build a `class="..."` attribute from one or more (hardcoded) class names.
     */
    protected function classes(string ...$names): string
    {
        return 'class="'.implode(' ', $names).'"';
    }

    /**
     * The stable, user-facing id for this widget (instance_id or the auto uid).
     *
     * @param  array<string,  mixed>  $options
     */
    protected function instanceId(array $options): string
    {
        $raw = $options['instance_id'] ?? null;

        return is_string($raw) && $raw !== '' ? $raw : $this->uid;
    }

    /**
     * A CSS-class-safe slug derived from instance_id (falls back to the uid).
     *
     * @param  array<string,  mixed>  $options
     */
    protected function instanceSlug(array $options): string
    {
        $raw = (string) ($options['instance_id'] ?? '');
        $slug = preg_replace('/[^a-zA-Z0-9_-]/', '', $raw);

        return $slug === null || $slug === '' ? $this->uid : $slug;
    }
}
