<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Light;

use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\Tuple;

final readonly class Light
{
    public function __construct(private Tuple $position, private Color $intensity)
    {
    }

    public function isEqualTo(Light $light): bool
    {
        return $this->position->isEqualTo($light->position) && $this->intensity->isEqualTo($light->intensity);
    }

    public function getPosition(): Tuple
    {
        return $this->position;
    }

    public function getIntensity(): Color
    {
        return $this->intensity;
    }
}
