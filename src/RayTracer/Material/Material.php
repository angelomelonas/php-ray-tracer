<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Material;

use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Pattern\Pattern;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use function pow;

final class Material
{
    public function __construct(
        private Color $color = new Color(1, 1, 1),
        private float $ambient = 0.1,
        private float $diffuse = 0.9,
        private float $specular = 0.9,
        private float $shininess = 200.0,
        private float $reflective = 0.0, // TODO: maybe rename to reflectiveIndex
        private float $transparency = 0.0,
        private float $refractiveIndex = 1.0,
        private ?Pattern $pattern = null,
    ) {
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function setColor(Color $color): void
    {
        $this->color = $color;
    }

    public function getAmbient(): float
    {
        return $this->ambient;
    }

    public function setAmbient(float $ambient): void
    {
        $this->ambient = $ambient;
    }

    public function getDiffuse(): float
    {
        return $this->diffuse;
    }

    public function setDiffuse(float $diffuse): void
    {
        $this->diffuse = $diffuse;
    }

    public function getSpecular(): float
    {
        return $this->specular;
    }

    public function setSpecular(float $specular): void
    {
        $this->specular = $specular;
    }

    public function getShininess(): float
    {
        return $this->shininess;
    }

    public function setShininess(float $shininess): void
    {
        $this->shininess = $shininess;
    }

    public function getReflective(): float
    {
        return $this->reflective;
    }

    public function setReflective(float $reflective): void
    {
        $this->reflective = $reflective;
    }

    public function getTransparency(): float
    {
        return $this->transparency;
    }

    public function setTransparency(float $transparency): void
    {
        $this->transparency = $transparency;
    }

    public function getRefractiveIndex(): float
    {
        return $this->refractiveIndex;
    }

    public function setRefractiveIndex(float $refractiveIndex): void
    {
        $this->refractiveIndex = $refractiveIndex;
    }

    public function getPattern(): ?Pattern
    {
        return $this->pattern;
    }

    public function setPattern(?Pattern $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function lighting(
        Shape $shape,
        Light $light,
        Tuple $position,
        Tuple $eyeVector,
        Tuple $normalVector,
        bool $inShadow,
    ): Color {
        if ($this->pattern !== null) {
            $color = $this->pattern->patternAtShape($shape, $position);
        } else {
            $color = $this->color;
        }

        $effectiveColor = $color->hadamardProduct($light->getIntensity());
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
