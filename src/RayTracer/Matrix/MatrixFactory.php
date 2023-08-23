<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Matrix;

use PhpRayTracer\RayTracer\Tuple\Tuple;
use function cos;
use function sin;

final class MatrixFactory
{
    public const MATRIX_2X2 = 2;
    public const MATRIX_3X3 = 3;
    public const MATRIX_4X4 = 4;

    public static function create(int $size): Matrix
    {
        return new Matrix($size);
    }

    public static function create2By2(): Matrix
    {
        return new Matrix(self::MATRIX_2X2);
    }

    public static function create3By3(): Matrix
    {
        return new Matrix(self::MATRIX_3X3);
    }

    public static function create4By4(): Matrix
    {
        return new Matrix(self::MATRIX_4X4);
    }

    public static function createIdentity(int $size = self::MATRIX_4X4): Matrix
    {
        $matrix = self::create($size);
        for ($i = 0; $i < $size; $i++) {
            $matrix->set($i, $i, 1);
        }

        return $matrix;
    }

    public static function createTranslation(float $x, float $y, float $z): Matrix
    {
        $size = self::MATRIX_4X4;

        $matrix = self::createIdentity($size);
        $matrix->set(0, $size - 1, $x);
        $matrix->set(1, $size - 1, $y);
        $matrix->set(2, $size - 1, $z);
        $matrix->set(3, $size - 1, Tuple::POINT);

        return $matrix;
    }

    public static function createScaling(float $x, float $y, float $z): Matrix
    {
        $matrix = self::createIdentity(self::MATRIX_4X4);
        $matrix->set(0, 0, $x);
        $matrix->set(1, 1, $y);
        $matrix->set(2, 2, $z);
        $matrix->set(3, 3, Tuple::POINT);

        return $matrix;
    }

    public static function createRotationX(float $angle): Matrix
    {
        $matrix = self::createIdentity(self::MATRIX_4X4);
        $matrix->set(1, 1, cos($angle));
        $matrix->set(1, 2, -sin($angle));
        $matrix->set(2, 1, sin($angle));
        $matrix->set(2, 2, cos($angle));

        return $matrix;
    }

    public static function createRotationY(float $angle): Matrix
    {
        $matrix = self::createIdentity(self::MATRIX_4X4);
        $matrix->set(0, 0, cos($angle));
        $matrix->set(0, 2, sin($angle));
        $matrix->set(2, 0, -sin($angle));
        $matrix->set(2, 2, cos($angle));

        return $matrix;
    }

    public static function createRotationZ(float $angle): Matrix
    {
        $matrix = self::createIdentity(self::MATRIX_4X4);
        $matrix->set(0, 0, cos($angle));
        $matrix->set(0, 1, -sin($angle));
        $matrix->set(1, 0, sin($angle));
        $matrix->set(1, 1, cos($angle));

        return $matrix;
    }

    public static function createShearing(float $xY, float $xZ, float $yX, float $yZ, float $zX, float $zY): Matrix
    {
        $matrix = self::createIdentity(self::MATRIX_4X4);
        $matrix->set(0, 1, $xY);
        $matrix->set(0, 2, $xZ);
        $matrix->set(1, 0, $yX);
        $matrix->set(1, 2, $yZ);
        $matrix->set(2, 0, $zX);
        $matrix->set(2, 1, $zY);

        return $matrix;
    }

    public static function createViewTransformation(Tuple $from, Tuple $to, Tuple $up): Matrix
    {
        $forward = $to->subtract($from)->normalize();
        $upn = $up->normalize();
        $left = $forward->cross($upn);
        $trueUp = $left->cross($forward);

        $orientation = self::createIdentity(4);
        $orientation->set(0, 0, $left->x);
        $orientation->set(0, 1, $left->y);
        $orientation->set(0, 2, $left->z);
        $orientation->set(1, 0, $trueUp->x);
        $orientation->set(1, 1, $trueUp->y);
        $orientation->set(1, 2, $trueUp->z);
        $orientation->set(2, 0, -$forward->x);
        $orientation->set(2, 1, -$forward->y);
        $orientation->set(2, 2, -$forward->z);

        return $orientation->multiplyMatrix(self::createTranslation(-$from->x, -$from->y, -$from->z));
    }
}
