<?php

declare(strict_types=1);

namespace Emleons\SimRating\Interfaces;

interface RendererInterface
{
    /**
     * Render the rating to its string representation.
     */
    public function render(): string;
}
