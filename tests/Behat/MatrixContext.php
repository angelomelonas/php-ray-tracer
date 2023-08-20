<?php

namespace PhpRayTracer\Tests\Behat;

use _PHPStan_7c8075089\Symfony\Component\Console\Exception\LogicException;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\Utility\Utility;
use PHPUnit\Framework\Assert;

final class MatrixContext implements Context
{
    private Matrix $matrixA;
    private Matrix $matrixB;
    private Matrix $matrixC;

    private TupleContext $tupleContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
    }

    /**
     * @Given /^the following (\d+)x(\d+) matrix ([^"]+):$/
     */
    public function theFollowingMxNMatrixA(int $width, int $height, TableNode $table): void
    {
       if(!isset($this->matrixA)){
           $this->matrixA = MatrixFactory::create($width);
           $this->matrixA = $this->createMatrixFromTable($table);

           return;
       }

        if(!isset($this->matrixB)){
            $this->matrixB = MatrixFactory::create($width);
            $this->matrixB = $this->createMatrixFromTable($table);

            return;
        }

    }

    /**
     * @Then /^M\[(\d+),(\d+)\] = ([-+]?\d*\.?\d+)$/
     */
    public function isMatrixValue(int $row, int $column, float $value): void
    {
        Assert::assertSame($value, $this->matrixA->matrix[$row][$column]);
    }

    /**
     * @Given /^the following matrix ([^"]+):$/
     */
    public function theFollowingMatrix(string $matrixName, TableNode $table): void
    {
        $size = count($table->getRows());

        $matrix = $this->createMatrix($size);

        foreach ($table->getRows() as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $matrix->set($rowIndex, $columnIndex, (float)$value);
            }
        }
    }

    /**
     * @Then /^A = B$/
     */
    public function matrixAEqualsMatrixB(): void
    {
        Assert::assertTrue($this->matrixA->isEqualTo($this->matrixB));
    }

    /**
     * @Then /^A != B$/
     */
    public function matrixADoesNotEqualMatrixB(): void
    {
        Assert::assertFalse($this->matrixA->isEqualTo($this->matrixB));
    }

    /**
     * @Then /^A \* B is the following (\d+)x(\d+) matrix:$/
     */
    public function aMultipliedByBIsTheFollowingMatrix(int $rows, int $columns, TableNode $table): void
    {
        $expectedMatrix = $this->createMatrixFromTable($table);

        $actualMatrix = $this->matrixA->multiplyMatrix($this->matrixB);

        Assert::assertTrue($expectedMatrix->isEqualTo($actualMatrix));
    }

    /**
     * @Then /^A \* b = tuple\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function matrixAMultipliedByTupleBIsATuple(float $x, float $y, float $z, float $w): void
    {
        $result = $this->matrixA->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue(TupleFactory::create($x, $y, $z, $w)->isEqualTo($result));
    }

    /**
     * @Then /^A \* identity_matrix = A$/
     */
    public function matrixAMultipliedByTheIdentityMatrix(): void
    {
        $result = $this->matrixA->multiplyMatrix(MatrixFactory::createIdentity($this->matrixA->size));
        Assert::assertTrue($this->matrixA->isEqualTo($result));
    }

    /**
     * @Then /^identity_matrix \* a = a$/
     */
    public function tupleAMultipliedByTheIdentityMatrix(): void
    {
        $result = MatrixFactory::createIdentity(4)->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue($this->tupleContext->tupleA->isEqualTo($result));
    }

    /**
     * @Then /^transpose\(([^"]+)\) is the following matrix:$/
     */
    public function transposeAIsTheFollowingMatrix(TableNode $table): void
    {
        $expectedMatrix = $this->createMatrixFromTable($table);
        $actualMatrix = $this->matrixA->transpose();
        Assert::assertTrue($expectedMatrix->isEqualTo($actualMatrix));
    }

    /**
     * @Given /^A is a transpose\(([^"]+)\)$/
     */
    public function aIsATransposeIdentityMatrix(): void
    {
        $this->matrixA = MatrixFactory::createIdentity(4)->transpose();
    }

    /**
     * @Then /^A = identity_matrix$/
     */
    public function matrixAEqualsTheIdentityMatrix(): void
    {
        Assert::assertTrue($this->matrixA->isEqualTo(MatrixFactory::createIdentity(4)));
    }

    /**
     * @Then /^determinant\(A\) = (-?\d+$)$/
     */
    public function determinantOfMatrixA(float $value): void
    {
        $result = $this->matrixA->determinant();
        Assert::assertTrue(Utility::areFloatsEqual($value, $result));
    }

    /**
     * @Then /^submatrix\(([^"]+), (\d+), (\d+)\) is the following (2x2|3x3) matrix:$/
     */
    public function subMatrixAIsTheFollowingMatrix(string $matrixName, int $row, int $column, TableNode $table): void
    {
        $expectedMatrix = $this->createMatrixFromTable($table);
        $actualMatrix = $this->matrixA->subMatrix($row, $column);
        Assert::assertTrue($expectedMatrix->isEqualTo($actualMatrix));
    }

    /**
     * @Given /^B is a submatrix\(([^"]+), (\d+), (\d+)\)$/
     */
    public function bIsASubmatrix(string $matrixName, int $row, int $column): void
    {
        $this->matrixB = $this->matrixA->subMatrix($row, $column);
    }

    /**
     * @Then /^determinant\(B\) = (-?\d+$)$/
     */
    public function determinantOfMatrixB(float $value): void
    {
        $result = $this->matrixB->determinant();
        Assert::assertTrue(Utility::areFloatsEqual($value, $result));
    }

    /**
     * @Given /^minor\(([^"]+), (\d+), (\d+)\) = (-?\d+$)$/
     */
    public function minorOfMatrix(string $matrixName, int $row, int $column, float $value): void
    {
        $result = $this->matrixA->minor($row, $column);
        Assert::assertTrue(Utility::areFloatsEqual($value, $result));
    }

    /**
     * @Given /^cofactor\(([^"]+), (\d+), (\d+)\) = (-?\d+$)$/
     */
    public function cofactorMatrix(string $matrixName, int $row, int $column, float $value): void
    {
        $result = $this->matrixA->cofactor($row, $column);
        Assert::assertTrue(Utility::areFloatsEqual($value, $result));
    }

    /**
     * @Given /^([^"]+) is invertible$/
     */
    public function aIsInvertible(): void
    {
        Assert::assertTrue($this->matrixA->invertible());
    }

    /**
     * @Given /^([^"]+) is not invertible$/
     */
    public function aIsNotInvertible(): void
    {
        Assert::assertFalse($this->matrixA->invertible());
    }

    /**
     * @Given /^B is a inverse\(([^"]+)\)$/
     */
    public function bIsTheInverseOfA(): void
    {
        $this->matrixB = $this->matrixA->inverse();
    }

    /**
     * @Given /^B\[(\d+),(\d+)\] = ([-+]?\d*\.?\d+)\/([-+]?\d*\.?\d+)/
     */
    public function setMatrixBValue(int $row, int $column, float $a, float $b): void
    {
        $this->matrixB->set($row, $column, $a / $b);
    }

    /**
     * @Given /^B is the following 4x4 matrix:$/
     */
    public function bIsTheFollowingMatrix(TableNode $table): void
    {
        $expected = $this->createMatrixFromTable($table);
        Assert::assertTrue($expected->isEqualTo($this->matrixB));
    }

    /**
     * @Then /^inverse\(A\) is the following 4x4 matrix:$/
     */
    public function inverseOfAIsTheFollowingMatrix(TableNode $table): void
    {
        $expected = $this->createMatrixFromTable($table);
        $actual = $this->matrixA->inverse();
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /**
     * @Given /^C is a A \* B$/
     */
    public function matrixCIsAMultipliedByB(): void
    {
        $this->matrixC = $this->matrixA->multiplyMatrix($this->matrixB);
    }

    /**
     * @Then /^C \* inverse\(B\) = A$/
     */
    public function matrixAIsMatrixCMultipliedByInverseB(): void
    {
        $result = $this->matrixC->multiplyMatrix($this->matrixB->inverse());
        Assert::assertTrue($result->isEqualTo($this->matrixA));
    }

    private function createMatrix(int $size): Matrix
    {
        if (!isset($this->matrixA)) {
            $this->matrixA = MatrixFactory::create($size);

            return $this->matrixA;
        }

        if (!isset($this->matrixB)) {
            $this->matrixB = MatrixFactory::create($size);

            return $this->matrixB;
        }

        throw new LogicException('No color is set.');
    }

    private function createMatrixFromTable(TableNode $table): Matrix
    {
        $size = count($table->getRows());
        $matrix = new Matrix($size);
        foreach ($table->getRows() as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $matrix->set($rowIndex, $columnIndex, floatval($value));
            }
        }

        return $matrix;
    }
}