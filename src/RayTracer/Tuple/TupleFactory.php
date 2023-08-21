<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Tuple;

final class TupleFactory
{
    public static function create(float $x, float $y, float $z, float $w): Tuple
    {
//        if ($w !== 0.0 && $w !== 1.0) {
//            throw new LogicException('Not a valid tuple (neither a point nor a vector).');
//        }

        return new Tuple($x, $y, $z, $w);
    }

    public static function createVector(float $x, float $y, float $z): Tuple
    {
        return new Tuple($x, $y, $z, 0.0);
    }

    public static function createPoint(float $x, float $y, float $z): Tuple
    {
        return new Tuple($x, $y, $z, 1.0);
    }
}
