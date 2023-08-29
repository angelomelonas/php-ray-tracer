<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Pattern;

use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use function floor;

final class CheckerPattern extends Pattern
{
    public function __construct(private readonly Color $colorA, private readonly Color $colorB)
    {
        parent::__construct();
    }

    protected function patternAt(Tuple $point): Color
    {
        if ((floor($point->getX()) + floor($point->getY()) + floor($point->getZ())) % 2 === 0) {
            return $this->colorA;
        }

        return $this->colorB;
    }
}
