<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Tuple;

use PhpRayTracer\RayTracer\Utility\Utility;
use function pow;
use function sprintf;
use function sqrt;

class Tuple
{
    public const POINT = 1.0;
    public const VECTOR = 0.0;

    public function __construct(public float $x, public float $y, public float $z, public float $w)
    {
    }

    public function isPoint(): bool
    {
        return $this->w === self::POINT;
    }

    public function isVector(): bool
    {
        return $this->w === self::VECTOR;
    }

    public function isEqualTo(Tuple $tuple): bool
    {
        return Utility::areFloatsEqual($this->x, $tuple->x)
            && Utility::areFloatsEqual($this->y, $tuple->y)
            && Utility::areFloatsEqual($this->z, $tuple->z)
            && Utility::areFloatsEqual($this->w, $tuple->w);
    }

    public function add(Tuple $tuple): Tuple
    {
        return new Tuple(
            $this->x + $tuple->x,
            $this->y + $tuple->y,
            $this->z + $tuple->z,
            $this->w + $tuple->w
        );
    }

    public function subtract(Tuple $tuple): Tuple
    {
        return new Tuple(
            $this->x - $tuple->x,
            $this->y - $tuple->y,
            $this->z - $tuple->z,
            $this->w - $tuple->w
        );
    }

    public function negate(): Tuple
    {
        return new Tuple(
            -$this->x,
            -$this->y,
            -$this->z,
            -$this->w
        );
    }

    public function multiply(float $scalar): Tuple
    {
        return new Tuple(
            $this->x * $scalar,
            $this->y * $scalar,
            $this->z * $scalar,
            $this->w * $scalar
        );
    }

    public function divide(float $scalar): Tuple
    {
        return new Tuple(
            $this->x / $scalar,
            $this->y / $scalar,
            $this->z / $scalar,
            $this->w / $scalar
        );
    }

    public function magnitude(): float
    {
        return sqrt(
            pow($this->x, 2) +
            pow($this->y, 2) +
            pow($this->z, 2) +
            pow($this->w, 2)
        );
    }

    public function normalize(): Tuple
    {
        $magnitude = $this->magnitude();

        return new Tuple(
            $this->x / $magnitude,
            $this->y / $magnitude,
            $this->z / $magnitude,
            $this->w / $magnitude
        );
    }

    public function dot(Tuple $tuple): float
    {
        return $this->x * $tuple->x
            + $this->y * $tuple->y
            + $this->z * $tuple->z
            + $this->w * $tuple->w;
    }

    public function cross(Tuple $tuple): Tuple
    {
        return TupleFactory::createVector(
            $this->y * $tuple->z - $this->z * $tuple->y,
            $this->z * $tuple->x - $this->x * $tuple->z,
            $this->x * $tuple->y - $this->y * $tuple->x
        );
    }

    public function __toString(): string
    {
        return sprintf('(%.2f, %.2f, %.2f)', $this->x, $this->y, $this->z);
    }
}
