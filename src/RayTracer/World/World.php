<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\World;

use PhpRayTracer\RayTracer\Intersection\Computation;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Ray\RayFactory;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use function assert;
use function sqrt;

final class World
{
    public const DEFAULT_REFLECTION_RECURSION_DEPTH = 4;

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

    public function shadeHit(Computation $computation, int $remaining = self::DEFAULT_REFLECTION_RECURSION_DEPTH): Color
    {
        assert($this->light !== null);

        $isShadowed = $this->isShadowed($computation->getOverPoint());

        $surface = $computation->getShape()->getMaterial()->lighting(
            $computation->getShape(),
            $this->light,
            $computation->getOverPoint(),
            $computation->getEyeVector(),
            $computation->getNormalVector(),
            $isShadowed
        );

        $reflectedColor = $this->reflectedColor($computation, $remaining);
        $refractedColor = $this->refractedColor($computation, $remaining);

        $material = $computation->getShape()->getMaterial();
        if ($material->getReflective() > 0.0 && $material->getTransparency() > 0.0) {
            $reflectance = $computation->schlick();

            return $surface->add($reflectedColor->multiply($reflectance))->add($refractedColor->multiply(1.0 - $reflectance));
        }

        return $surface->add($reflectedColor)->add($refractedColor);
    }

    public function colorAt(Ray $ray, int $remaining = self::DEFAULT_REFLECTION_RECURSION_DEPTH): Color
    {
        $intersections = $this->intersectWorld($ray);
        $hit = $intersections->hit();

        if ($hit === null) {
            return ColorFactory::createBlack();
        }

        $computation = $hit->prepareComputations($ray, $intersections);

        return $this->shadeHit($computation, $remaining);
    }

    public function isShadowed(Tuple $point): bool
    {
        assert($this->light !== null);

        $y = $this->light->getPosition()->subtract($point);
        $distance = $y->magnitude();
        $direction = $y->normalize();

        $ray = RayFactory::create($point, $direction);
        $intersections = $this->intersectWorld($ray);

        $hit = $intersections->hit();

        return $hit !== null && $hit->getT() < $distance;
    }

    public function reflectedColor(Computation $computation, int $remaining): Color
    {
        if ($remaining < 1) {
            return ColorFactory::createBlack();
        }

        if ($computation->getShape()->getMaterial()->getReflective() === 0.0) {
            return ColorFactory::createBlack();
        }

        $reflectRay = RayFactory::create($computation->getOverPoint(), $computation->getReflectVector());
        $color = $this->colorAt($reflectRay, $remaining - 1);

        return $color->multiply($computation->getShape()->getMaterial()->getReflective());
    }

    public function refractedColor(Computation $computation, int $remaining): Color
    {
        if ($remaining === 0) {
            return ColorFactory::createBlack();
        }

        $nRatio = $computation->getN1() / $computation->getN2();
        $cosI = $computation->getEyeVector()->dot($computation->getNormalVector());
        $sin2T = ($nRatio ** 2) * (1 - ($cosI ** 2));

        if ($sin2T > 1.0) {
            return ColorFactory::createBlack();
        }

        $cosT = sqrt(1.0 - $sin2T);
        $direction = $computation->getNormalVector()->multiply($nRatio * $cosI - $cosT)->subtract($computation->getEyeVector()->multiply($nRatio));
        $refractRay = RayFactory::create($computation->getUnderPoint(), $direction);

        return $this->colorAt($refractRay, $remaining - 1)
            ->multiply($computation->getShape()->getMaterial()->getTransparency());
    }
}
