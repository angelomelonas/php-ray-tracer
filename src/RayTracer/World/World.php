<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\World;

use PhpRayTracer\RayTracer\Intersection\Computation;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use function assert;

final class World
{
    /** @var Shape[] */
    private array $shapes;
    private ?Light $light;

    public function __construct()
    {
        $this->shapes = [];
        $this->light = null;
    }

    public function getShape(int $index): Shape
    {
        return $this->shapes[$index];
    }

    public function addShape(Shape $shape): void
    {
        $this->shapes[] = $shape;
    }

    public function isEmpty(): bool
    {
        return empty($this->shapes);
    }

    public function getLight(): ?Light
    {
        return $this->light;
    }

    public function setLight(Light $light): void
    {
        $this->light = $light;
    }

    public function intersectWorld(Ray $ray): Intersections
    {
        $intersections = new Intersections();

        foreach ($this->shapes as $shape) {
            foreach ($shape->intersect($ray) as $intersection) {
                $intersections->add($intersection);
            }
        }

        return $intersections;
    }

    public function shadeHit(Computation $computation): Color
    {
        assert($this->light !== null);

        return $computation->getObject()->getMaterial()->lighting(
            $this->light,
            $computation->getPoint(),
            $computation->getEyeVector(),
            $computation->getNormalVector(),
        );
    }

    public function colorAt(Ray $ray): Color
    {
        $intersections = $this->intersectWorld($ray);
        $hit = $intersections->hit();

        if ($hit === null) {
            return ColorFactory::createBlack();
        }

        $computation = $hit->prepareComputations($ray);

        return $this->shadeHit($computation);
    }
}
