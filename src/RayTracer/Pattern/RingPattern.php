<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Pattern;

use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use function floor;
use function pow;
use function sqrt;

final class RingPattern extends Pattern
{
    public function __construct(private readonly Color $colorA, private readonly Color $colorB)
    {
        parent::__construct();
    }

    protected function patternAt(Tuple $point): Color
    {
        if (floor(sqrt(pow($point->getX(), 2) + pow($point->getZ(), 2))) % 2 === 0) {
            return $this->colorA;
        }

        return $this->colorB;
    }
}
