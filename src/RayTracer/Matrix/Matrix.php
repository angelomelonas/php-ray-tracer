<?php

namespace PhpRayTracer\RayTracer\Matrix;

use _PHPStan_7c8075089\Symfony\Component\Console\Exception\LogicException;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\Utility\Utility;

final class Matrix
{
    public const ZERO_VALUE = 0.0;

    /**
     * @var array<array<float>>
     */
    public array $matrix = [];

    public function __construct(public int $size)
    {
        $this->matrix = array_fill(
            0,
            $this->size,
            array_fill(0, $this->size, self::ZERO_VALUE)
        );
    }

    public function set(int $row, int $column, float $value): void
    {
        $this->matrix[$row][$column] = $value;
    }

    public function isEqualTo(Matrix $matrixB): bool
    {
        if ($this->size !== $matrixB->size) {
            return false;
        }

        for ($row = 0; $row < $this->size; $row++) {
            for ($column = 0; $column < $this->size; $column++) {
                if (Utility::areFloatsEqual($this->matrix[$row][$column], $matrixB->matrix[$row][$column]) === false) {
                    return false;
                }
            }
        }

        return true;
    }

    public function multiplyMatrix(Matrix $matrix): Matrix
    {
        if ($this->size !== 4) {
            throw new LogicException('Only 4x4 matrices can be multiplied.');
        }

        $newMatrix = MatrixFactory::create($this->size);

        for ($row = 0; $row < $this->size; $row++) {
            for ($column = 0; $column < $this->size; $column++) {
                $newMatrix->set($row, $column,
                    $this->matrix[$row][0] * $matrix->matrix[0][$column]
                    + $this->matrix[$row][1] * $matrix->matrix[1][$column]
                    + $this->matrix[$row][2] * $matrix->matrix[2][$column]
                    + $this->matrix[$row][3] * $matrix->matrix[3][$column]
                );
            }
        }

        return $newMatrix;
    }

    public function multiplyTuple(Tuple $tuple): Tuple
    {
        if ($this->size !== 4) {
            throw new LogicException('Only 4x4 matrices can be multiplied.');
        }

        $result = [];
        for ($i = 0; $i < $this->size; $i++) {
            $result[$i] =
                $this->matrix[$i][0] * $tuple->x
                + $this->matrix[$i][1] * $tuple->y
                + $this->matrix[$i][2] * $tuple->z
                + $this->matrix[$i][3] * $tuple->w;
        }

        return TupleFactory::create($result[0], $result[1], $result[2], $result[3]);
    }

    public function transpose(): Matrix
    {
        $newMatrix = MatrixFactory::create($this->size);

        for ($row = 0; $row < $this->size; $row++) {
            for ($column = 0; $column < $this->size; $column++) {
                $newMatrix->set($row, $column, $this->matrix[$column][$row]);
            }
        }

        return $newMatrix;
    }

    public function determinant(): float
    {
        if ($this->size === 2) {
            return $this->matrix[0][0] * $this->matrix[1][1] - $this->matrix[0][1] * $this->matrix[1][0];
        }

        $determinant = 0.0;
        for ($column = 0; $column < $this->size; $column++) {
            $determinant += $this->matrix[0][$column] * $this->cofactor(0, $column);
        }

        return $determinant;
    }

    public function subMatrix(int $row, int $column): Matrix
    {
        $subMatrix = MatrixFactory::create($this->size - 1);

        $subMatrixRow = 0;
        for ($i = 0; $i < $this->size; $i++) {
            if ($i !== $row) {
                $subMatrixColumn = 0;
                for ($j = 0; $j < $this->size; $j++) {
                    if ($j !== $column) {
                        $subMatrix->set($subMatrixRow, $subMatrixColumn, $this->matrix[$i][$j]);
                        $subMatrixColumn++;
                    }
                }
                $subMatrixRow++;
            }
        }

        return $subMatrix;
    }

    public function minor(int $row, int $column): float
    {
        return $this->subMatrix($row, $column)->determinant();
    }

    public function cofactor(int $row, int $column): float
    {
        $minor = $this->minor($row, $column);

        if (($row + $column) % 2 === 0) {
            return $minor;
        }

        return -$minor;
    }

    public function invertible(): bool
    {
        return $this->determinant() !== 0.0;
    }

    public function inverse(): Matrix
    {
        $determinant = $this->determinant();
        $newMatrix = MatrixFactory::create($this->size);

        for ($row = 0; $row < $this->size; $row++) {
            for ($column = 0; $column < $this->size; $column++) {
                $cofactor = $this->cofactor($row, $column);
                $newMatrix->set($column, $row, $cofactor / $determinant);
            }
        }

        return $newMatrix;
    }
}