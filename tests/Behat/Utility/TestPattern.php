<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat\Utility;

use PhpRayTracer\RayTracer\Pattern\Pattern;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\Tuple;

final class TestPattern extends Pattern
{
    protected function patternAt(Tuple $point): Color
    {
        return new Color($point->x, $point->y, $point->z);
    }
}
