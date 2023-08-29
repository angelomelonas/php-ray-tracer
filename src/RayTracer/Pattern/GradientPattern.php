<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Pattern;

use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use function floor;

final class GradientPattern extends Pattern
{
    public function __construct(private readonly Color $colorA, private readonly Color $colorB)
    {
        parent::__construct();
    }

    protected function patternAt(Tuple $point): Color
    {
        $distance = $this->colorB->subtract($this->colorA);
        $fraction = $point->getX() - floor($point->getX());

        return $this->colorA->add($distance->multiply($fraction));
    }
}
