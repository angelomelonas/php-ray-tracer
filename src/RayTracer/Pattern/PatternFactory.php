<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Pattern;

use PhpRayTracer\RayTracer\Tuple\Color;

final class PatternFactory
{
    public static function createStripePattern(Color $colorA, Color $colorB): Pattern
    {
            return new StripePattern($colorA, $colorB);
    }

    public static function createGradientPattern(Color $colorA, Color $colorB): Pattern
    {
            return new GradientPattern($colorA, $colorB);
    }

    public static function createRingPattern(Color $colorA, Color $colorB): Pattern
    {
            return new RingPattern($colorA, $colorB);
    }

    public static function createCheckerPattern(Color $colorA, Color $colorB): Pattern
    {
            return new CheckerPattern($colorA, $colorB);
    }
}
