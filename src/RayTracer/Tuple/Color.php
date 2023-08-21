<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Tuple;

use PhpRayTracer\RayTracer\Utility\Utility;
use function max;
use function min;
use function round;
use function sprintf;

final class Color
{
    public function __construct(public float $red, public float $green, public float $blue)
    {
    }

    public function add(Color $color): Color
    {
        return new Color($this->red + $color->red, $this->green + $color->green, $this->blue + $color->blue);
    }

    public function isEqualTo(Color $color): bool
    {
        return Utility::areFloatsEqual($this->red, $color->red)
            && Utility::areFloatsEqual($this->green, $color->green)
            && Utility::areFloatsEqual($this->blue, $color->blue);
    }

    public function subtract(Color $color): Color
    {
        return new Color(
            $this->red - $color->red,
            $this->green - $color->green,
            $this->blue - $color->blue
        );
    }

    public function multiply(float $scalar): Color
    {
        return new Color(
            $this->red * $scalar,
            $this->green * $scalar,
            $this->blue * $scalar
        );
    }

    public function hadamardProduct(Color $color): Color
    {
        return new Color(
            $this->red * $color->red,
            $this->green * $color->green,
            $this->blue * $color->blue
        );
    }

    public function scale(): Color
    {
        return new self(
            round($this->red * 255),
            round($this->green * 255),
            round($this->blue * 255),
        );
    }

    public function clamp(): Color
    {
        return new self(
            max(0, min(255, $this->red)),
            max(0, min(255, $this->green)),
            max(0, min(255, $this->blue))
        );
    }

    public function __toString(): string
    {
        return sprintf('%s %s %s', $this->red, $this->green, $this->blue);
    }
}
