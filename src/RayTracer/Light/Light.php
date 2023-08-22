<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Light;

use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\Tuple;

final class Light
{
    public function __construct(public Tuple $position, public Color $intensity)
    {
    }
}
