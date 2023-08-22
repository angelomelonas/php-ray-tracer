<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use LogicException;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\Utility\Utility;
use PHPUnit\Framework\Assert;
use function count;
use function floatval;
use function sqrt;
use const M_PI_2;
use const M_PI_4;

final class MatrixContext implements Context
{
    public Matrix $matrixA;
    public Matrix $matrixB;
    public Matrix $matrixC;

    public Matrix $transform;
    public Matrix $inverse;
    //  TODO: Use this: private Matrix $translation;
    public Matrix $rotation;
    public Matrix $scaling;

    private Matrix $rotationHalfQuarter;
    private Matrix $rotationFullQuarter;

    private TupleContext $tupleContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
    }

    /** @Given /^the following (\d+)x(\d+) matrix ([^"]+):$/ */
    public function theFollowingMxNMatrixA(int $width, int $height, TableNode $table): void
    {
        if (! isset($this->matrixA)) {
            $this->matrixA = MatrixFactory::create($width);
            $this->matrixA = $this->createMatrixFromTable($table);

            return;
        }

        if (! isset($this->matrixB)) {
            $this->matrixB = MatrixFactory::create($width);
            $this->matrixB = $this->createMatrixFromTable($table);

            return;
        }
    }

    /** @Then /^M\[(\d+),(\d+)\] = ([-+]?\d*\.?\d+)$/ */
    public function isMatrixValue(int $row, int $column, float $value): void
    {
        Assert::assertSame($value, $this->matrixA->matrix[$row][$column]);
    }

    /** @Given /^the following matrix ([^"]+):$/ */
    public function theFollowingMatrix(string $matrixName, TableNode $table): void
    {
        $size = count($table->getRows());

        $matrix = $this->createMatrix($size);

        foreach ($table->getRows() as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $matrix->set($rowIndex, $columnIndex, (float) $value);
            }
        }
    }

    /** @Then /^A = B$/ */
    public function matrixAEqualsMatrixB(): void
    {
        Assert::assertTrue($this->matrixA->isEqualTo($this->matrixB));
    }

    /** @Then /^A != B$/ */
    public function matrixADoesNotEqualMatrixB(): void
    {
        Assert::assertFalse($this->matrixA->isEqualTo($this->matrixB));
    }

    /** @Then /^A \* B is the following (\d+)x(\d+) matrix:$/ */
    public function aMultipliedByBIsTheFollowingMatrix(int $rows, int $columns, TableNode $table): void
    {
        $expectedMatrix = $this->createMatrixFromTable($table);

        $actualMatrix = $this->matrixA->multiplyMatrix($this->matrixB);

        Assert::assertTrue($expectedMatrix->isEqualTo($actualMatrix));
    }

    /** @Then /^A \* b = tuple\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function matrixAMultipliedByTupleBIsATuple(float $x, float $y, float $z, float $w): void
    {
        $result = $this->matrixA->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue(TupleFactory::create($x, $y, $z, $w)->isEqualTo($result));
    }

    /** @Then /^A \* identity_matrix = A$/ */
    public function matrixAMultipliedByTheIdentityMatrix(): void
    {
        $result = $this->matrixA->multiplyMatrix(MatrixFactory::createIdentity($this->matrixA->size));
        Assert::assertTrue($this->matrixA->isEqualTo($result));
    }

    /** @Then /^identity_matrix \* a = a$/ */
    public function tupleAMultipliedByTheIdentityMatrix(): void
    {
        $result = MatrixFactory::createIdentity(4)->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue($this->tupleContext->tupleA->isEqualTo($result));
    }

    /** @Then /^transpose\(([^"]+)\) is the following matrix:$/ */
    public function transposeAIsTheFollowingMatrix(TableNode $table): void
    {
        $expectedMatrix = $this->createMatrixFromTable($table);
        $actualMatrix = $this->matrixA->transpose();
        Assert::assertTrue($expectedMatrix->isEqualTo($actualMatrix));
    }

    /** @Given /^A is a transpose\(([^"]+)\)$/ */
    public function aIsATransposeIdentityMatrix(): void
    {
        $this->matrixA = MatrixFactory::createIdentity(4)->transpose();
    }

    /** @Then /^A = identity_matrix$/ */
    public function matrixAEqualsTheIdentityMatrix(): void
    {
        Assert::assertTrue($this->matrixA->isEqualTo(MatrixFactory::createIdentity(4)));
    }

    /** @Then /^determinant\(A\) = ([-+]?\d*\.?\d+)$/ */
    public function determinantOfMatrixA(float $value): void
    {
        $result = $this->matrixA->determinant();
        Assert::assertTrue(Utility::areFloatsEqual($value, $result));
    }

    /** @Then /^submatrix\(([^"]+), (\d+), (\d+)\) is the following (2x2|3x3) matrix:$/ */
    public function subMatrixAIsTheFollowingMatrix(string $matrixName, int $row, int $column, TableNode $table): void
    {
        $expectedMatrix = $this->createMatrixFromTable($table);
        $actualMatrix = $this->matrixA->subMatrix($row, $column);
        Assert::assertTrue($expectedMatrix->isEqualTo($actualMatrix));
    }

    /** @Given /^B is a submatrix\(([^"]+), (\d+), (\d+)\)$/ */
    public function bIsASubmatrix(string $matrixName, int $row, int $column): void
    {
        $this->matrixB = $this->matrixA->subMatrix($row, $column);
    }

    /** @Then /^determinant\(B\) = (-?\d+$)$/ */
    public function determinantOfMatrixB(float $value): void
    {
        $result = $this->matrixB->determinant();
        Assert::assertTrue(Utility::areFloatsEqual($value, $result));
    }

    /** @Given /^minor\(([^"]+), (\d+), (\d+)\) = (-?\d+$)$/ */
    public function minorOfMatrix(string $matrixName, int $row, int $column, float $value): void
    {
        $result = $this->matrixA->minor($row, $column);
        Assert::assertTrue(Utility::areFloatsEqual($value, $result));
    }

    /** @Given /^cofactor\(([^"]+), (\d+), (\d+)\) = (-?\d+$)$/ */
    public function cofactorMatrix(string $matrixName, int $row, int $column, float $value): void
    {
        $result = $this->matrixA->cofactor($row, $column);
        Assert::assertTrue(Utility::areFloatsEqual($value, $result));
    }

    /** @Given /^([^"]+) is invertible$/ */
    public function aIsInvertible(): void
    {
        Assert::assertTrue($this->matrixA->invertible());
    }

    /** @Given /^([^"]+) is not invertible$/ */
    public function aIsNotInvertible(): void
    {
        Assert::assertFalse($this->matrixA->invertible());
    }

    /** @Given /^B is a inverse\(([^"]+)\)$/ */
    public function bIsTheInverseOfA(): void
    {
        $this->matrixB = $this->matrixA->inverse();
    }

    /** @Given /^B\[(\d+),(\d+)\] = ([-+]?\d*\.?\d+)\/([-+]?\d*\.?\d+)/ */
    public function setMatrixBValue(int $row, int $column, float $a, float $b): void
    {
        $this->matrixB->set($row, $column, $a / $b);
    }

    /** @Given /^B is the following 4x4 matrix:$/ */
    public function bIsTheFollowingMatrix(TableNode $table): void
    {
        $expected = $this->createMatrixFromTable($table);
        Assert::assertTrue($expected->isEqualTo($this->matrixB));
    }

    /** @Then /^inverse\(A\) is the following 4x4 matrix:$/ */
    public function inverseOfAIsTheFollowingMatrix(TableNode $table): void
    {
        $expected = $this->createMatrixFromTable($table);
        $actual = $this->matrixA->inverse();
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /** @Given /^C is a A \* B$/ */
    public function matrixCIsAMultipliedByB(): void
    {
        $this->matrixC = $this->matrixA->multiplyMatrix($this->matrixB);
    }

    /** @Then /^C \* inverse\(B\) = A$/ */
    public function matrixAIsMatrixCMultipliedByInverseB(): void
    {
        $result = $this->matrixC->multiplyMatrix($this->matrixB->inverse());
        Assert::assertTrue($result->isEqualTo($this->matrixA));
    }

    /** @Given /^(transform|C) is a translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformIsATranslation(string $expression, float $x, float $y, float $z): void
    {
        $this->transform = MatrixFactory::createTranslation($x, $y, $z);
    }

    /** @Then /^transform \* p = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformPointP(float $x, float $y, float $z): void
    {
        $expected = $this->transform->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($expected));
    }

    /** @Given /^([^"]+) is a inverse\(transform\)$/ */
    public function invIsAInverseTransform(string $expression): void
    {
        $this->inverse = $this->transform->inverse();
    }

    /** @Then /^inv \* p = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function inverseTransformOfPointP(int $x, int $y, int $z): void
    {
        $expected = $this->inverse->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($expected));
    }

    /** @Then /^transform \* v = v$/ */
    public function transformMultipliedByVectorV(): void
    {
        $expected = $this->transform->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue($this->tupleContext->tupleA->isEqualTo($expected));
    }

    /** @Given /^transform is a scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformIsAScaling(float $x, float $y, float $z): void
    {
        $this->transform = MatrixFactory::createScaling($x, $y, $z);
    }

    /** @Then /^transform \* v = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformVectorV(float $x, float $y, float $z): void
    {
        $expected = $this->transform->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($expected));
    }

    /** @Then /^inv \* v = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function inverseMultipliedByVectorV(float $x, float $y, float $z): void
    {
        $expected = $this->inverse->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($expected));
    }

    /** @Given /^half_quarter is a rotation_x\(π \/ 4\)$/ */
    public function halfQuarterIsARotationX(): void
    {
        $this->rotationHalfQuarter = MatrixFactory::createRotationX(M_PI_4);
    }

    /** @Given /^full_quarter is a rotation_x\(π \/ 2\)$/ */
    public function fullQuarterIsARotationX(): void
    {
        $this->rotationFullQuarter = MatrixFactory::createRotationX(M_PI_2);
    }

    /** @Given /^half_quarter is a rotation_y\(π \/ 4\)$/ */
    public function halfQuarterIsARotationY(): void
    {
        $this->rotationHalfQuarter = MatrixFactory::createRotationY(M_PI_4);
    }

    /** @Given /^full_quarter is a rotation_y\(π \/ 2\)$/ */
    public function fullQuarterIsARotationY(): void
    {
        $this->rotationFullQuarter = MatrixFactory::createRotationY(M_PI_2);
    }

    /** @Given /^half_quarter is a rotation_z\(π \/ 4\)$/ */
    public function halfQuarterIsARotationZ(): void
    {
        $this->rotationHalfQuarter = MatrixFactory::createRotationZ(M_PI_4);
    }

    /** @Given /^full_quarter is a rotation_z\(π \/ 2\)$/ */
    public function fullQuarterIsARotationZ(): void
    {
        $this->rotationFullQuarter = MatrixFactory::createRotationZ(M_PI_2);
    }

    /** @Given /^([^"]+) is a inverse\(half_quarter\)$/ */
    public function invIsAInverseRotation(string $expression): void
    {
        $this->inverse = $this->rotationHalfQuarter->inverse();
    }

    /** @Then /^half_quarter \* p = point\(0, √2\/2, √2\/2\)$/ */
    public function halfQuarterMultipliedByPointP1(): void
    {
        $expected = TupleFactory::createPoint(0, sqrt(2) / 2, sqrt(2) / 2);
        $actual = $this->rotationHalfQuarter->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /** @Then /^half_quarter \* p = point\(√2\/2, 0, √2\/2\)$/ */
    public function halfQuarterMultipliedByPointP2(): void
    {
        $actual = $this->rotationHalfQuarter->multiplyTuple($this->tupleContext->tupleA);
        $expected = TupleFactory::createPoint(sqrt(2) / 2, 0, sqrt(2) / 2);
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /** @Then /^half_quarter \* p = point\(-√2\/2, √2\/2, 0\)$/ */
    public function halfQuarterMultipliedByPointP3(): void
    {
        $actual = $this->rotationHalfQuarter->multiplyTuple($this->tupleContext->tupleA);
        $expected = TupleFactory::createPoint(-sqrt(2) / 2, sqrt(2) / 2, 0);
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /** @Given /^full_quarter \* p = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function fullQuarterMultipliedByPointP(float $x, float $y, float $z): void
    {
        $expected = TupleFactory::createPoint($x, $y, $z);
        $actual = $this->rotationFullQuarter->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /** @Then /^inv \* p = point\(0, √2\/2, \-√2\/2\)$/ */
    public function inverseMultipliedByPointP(): void
    {
        $result = $this->inverse->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue(TupleFactory::createPoint(0, sqrt(2) / 2, -sqrt(2) / 2)->isEqualTo($result));
    }

    /** @Given /^transform is a shearing\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformIsAShearing(float $xY, float $xZ, float $yX, float $yZ, float $zX, float $zY): void
    {
        $this->transform = MatrixFactory::createShearing($xY, $xZ, $yX, $yZ, $zX, $zY);
    }

    /** @Given /^A is a rotation_x\(π \/ 2\)$/ */
    public function aIsARotationX(): void
    {
        $this->rotation = MatrixFactory::createRotationX(M_PI_2);
    }

    /** @Given /^(B) is a scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function bIsAScaling(string $expression, float $x, float $y, float $z): void
    {
        $this->scaling = MatrixFactory::createScaling($x, $y, $z);
    }

    /** @When /^([^"]+) is a A \* p$/ */
    public function p2IsAMatrixAMultipliedByPointP1(): void
    {
        $this->tupleContext->tupleB = $this->rotation->multiplyTuple($this->tupleContext->tupleA);
    }

    /** @Then /^p2 = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function p2IsAPoint(float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($this->tupleContext->tupleB));
    }

    /** @When /^p3 is a B \* p2$/ */
    public function p3IsAMatrixBMultipliedByPointP2(): void
    {
        $this->tupleContext->tupleC = $this->scaling->multiplyTuple($this->tupleContext->tupleB);
    }

    /** @Then /^p3 = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function p3IsAPoint(float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($this->tupleContext->tupleC));
    }

    /** @When /^p4 is a C \* p3$/ */
    public function p4IsAMatrixCMultipliedByPointP3(): void
    {
        $this->tupleContext->tupleD = $this->transform->multiplyTuple($this->tupleContext->tupleC);
    }

    /** @Then /^p4 = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function p4IsAPoint(float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($this->tupleContext->tupleD));
    }

    /** @When /^T is a C \* B \* A$/ */
    public function MatrixCMultipliedByBMultipliedByA(): void
    {
        $this->transform = $this->transform->multiplyMatrix($this->scaling->multiplyMatrix($this->rotation));
    }

    /** @Then /^T \* p = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function tMultipliedByAPointIsAPoint(float $x, float $y, float $z): void
    {
        $expected = TupleFactory::createPoint($x, $y, $z);
        $actual = $this->transform->multiplyTuple($this->tupleContext->tupleA);
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    private function createMatrix(int $size): Matrix
    {
        if (! isset($this->matrixA)) {
            $this->matrixA = MatrixFactory::create($size);

            return $this->matrixA;
        }

        if (! isset($this->matrixB)) {
            $this->matrixB = MatrixFactory::create($size);

            return $this->matrixB;
        }

        throw new LogicException('No matrix is set.');
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

    /** @When /^t is a view_transform\(from, to, up\)$/ */
    public function tIsAView_transformFromToUp(): void
    {
        throw new PendingException();
    }

    /** @Then /^t = identity_matrix$/ */
    public function tIdentityMatrix(): void
    {
        throw new PendingException();
    }

    /** @Then /^t = scaling\(\-1, (\d+), \-1\)$/ */
    public function tScaling1(): void
    {
        throw new PendingException();
    }

    /** @Then /^t = translation\(0, (\d+), \-8\)$/ */
    public function tTranslation8(): void
    {
        throw new PendingException();
    }

    /** @Then /^t is the following 4x4 matrix:$/ */
    public function tIsTheFollowingMatrix(): void
    {
        throw new PendingException();
    }
}
