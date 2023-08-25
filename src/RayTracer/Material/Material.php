<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Material;

use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use function pow;

final class Material
{
    public function __construct(
        public Color $color = new Color(1, 1, 1),
        public float $ambient = 0.1,
        public float $diffuse = 0.9,
        public float $specular = 0.9,
        public float $shininess = 200.0,
    ) {
    }

    public function lighting(
        Light $light,
        Tuple $position,
        Tuple $eyeVector,
        Tuple $normalVector,
        bool $inShadow,
    ): Color {
        $effectiveColor = $this->color->hadamardProduct($light->getIntensity());

        $lightVector = $light->getPosition()->subtract($position)->normalize();

        $ambient = $effectiveColor->multiply($this->ambient);

        $lightDotNormal = $lightVector->dot($normalVector);

        if ($lightDotNormal < 0) {
            // The light is on the other side of the surface.
            $diffuse = ColorFactory::createBlack();
            $specular = ColorFactory::createBlack();
        } else {
            $diffuse = $effectiveColor->multiply($this->diffuse)->multiply($lightDotNormal);
            $reflectVector = $lightVector->negate()->reflect($normalVector);
            $reflectDotEye = $reflectVector->dot($eyeVector);

            if ($reflectDotEye <= 0) {
                $specular = ColorFactory::createBlack();
            } else {
                $factor = pow($reflectDotEye, $this->shininess);
                $specular = $light->getIntensity()->multiply($this->specular)->multiply($factor);
            }
        }

        if ($inShadow) {
            return $ambient;
        }

        return $ambient->add($diffuse)->add($specular);
    }
}
