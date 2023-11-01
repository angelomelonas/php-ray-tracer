<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use LogicException;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Shape\Cylinder;
use PhpRayTracer\RayTracer\Shape\ShapeFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;
use function assert;
use const INF;

final class CylinderContext implements Context
{
    private ShapeContext $shapeContext;
    private TupleContext $tupleContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->shapeContext = $environment->getContext(ShapeContext::class);
        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
    }

    /** @Given /^(cyl) is a cylinder\(\)$/ */
    public function cIsACube(): void
    {
        $this->createCylinder();
    }

    /** @When /^(normal) is a local_normal_at\((cyl), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function normalIsALocalNormalAtCP(string $expression, string $shape, float $x, float $y, float $z): void
    {
        $point = TupleFactory::createPoint($x, $y, $z);
        $this->tupleContext->tupleA = $this->shapeContext->shapeA->normalAt($point);
    }

    /** @Then /^(cyl)\.(minimum) = (\-infinity)$/ */
    public function cylMinimumInfinity(): void
    {
        $cylinder = $this->shapeContext->shapeA;
        assert($cylinder instanceof Cylinder);

        Assert::assertEquals(-INF, $cylinder->minimum);
    }

    /** @Given /^(cyl)\.(maximum) = (infinity$)/ */
    public function cylMaximumInfinity(): void
    {
        $cylinder = $this->shapeContext->shapeA;
        assert($cylinder instanceof Cylinder);

        Assert::assertEquals(INF, $cylinder->maximum);
    }

    /** @Given /^(cyl)\.(minimum) is ([-+]?\d*\.?\d+)$/ */
    public function cylMinimumIs(string $expression1, string $expression2, float $value): void
    {
        $cylinder = $this->shapeContext->shapeA;
        assert($cylinder instanceof Cylinder);

        $cylinder->minimum = $value;
    }

    /** @Given /^(cyl)\.(maximum) is ([-+]?\d*\.?\d+)$/ */
    public function cylMaximumIs(string $expression1, string $expression2, float $value): void
    {
        $cylinder = $this->shapeContext->shapeA;
        assert($cylinder instanceof Cylinder);

        $cylinder->maximum = $value;
    }

    /** @Then /^(cyl)\.(closed) = (true)$/ */
    public function cylClosedFalse(): void
    {
        $cylinder = $this->shapeContext->shapeA;
        assert($cylinder instanceof Cylinder);

        Assert::assertTrue($cylinder->closed);
    }

    /** @Then /^(cyl)\.(closed) = (false)$/ */
    public function cylClosedTrue(): void
    {
        $cylinder = $this->shapeContext->shapeA;
        assert($cylinder instanceof Cylinder);

        Assert::assertFalse($cylinder->closed);
    }

    /** @Given /^(cyl)\.(closed) is (true)/ */
    public function cylClosedIsTrue(): void
    {
        $cylinder = $this->shapeContext->shapeA;
        assert($cylinder instanceof Cylinder);

        $cylinder->closed = true;
    }

    /** @Given /^(cyl)\.(closed) is (false)/ */
    public function cylClosedIsFalse(): void
    {
        $cylinder = $this->shapeContext->shapeA;
        assert($cylinder instanceof Cylinder);

        $cylinder->closed = false;
    }

    private function createCylinder(?Material $material = null, ?Matrix $transform = null): void
    {
        $newCylinder = ShapeFactory::createCylinder();
        if ($material) {
            $newCylinder->setMaterial($material);
        }

        if ($transform) {
            $newCylinder->setTransform($transform);
        }

        if (! isset($this->shapeContext->shapeA)) {
            $this->shapeContext->shapeA = $newCylinder;

            return;
        }

        throw new LogicException('No Cylinder is set.');
    }
}
