<?php

namespace PhpRayTracer\RayTracer\Matrix;

final class MatrixFactory
{
    public const MATRIX_2X2 = 2;
    public const MATRIX_3X3 = 3;
    public const MATRIX_4X4 = 4;

    public static function create(int $size): Matrix{
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

    public static function createIdentity(int $size): Matrix
    {
        $matrix = self::create($size);
        for ($i = 0; $i < $size; $i++) {
            $matrix->set($i, $i, 1);
        }

        return $matrix;
    }
}