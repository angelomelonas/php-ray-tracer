<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use LogicException;
use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Shape\ShapeFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;

final class PlaneContext implements Context
{
    private ShapeContext $shapeContext;

    /** @var Intersection[] */
    private array $localIntersections;

    private RayContext $rayContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->shapeContext = $environment->getContext(ShapeContext::class);
        /* @phpstan-ignore-next-line */
        $this->rayContext = $environment->getContext(RayContext::class);
    }

    /** @Given /^(p) is a plane\(\)$/ */
    public function pIsAPlane(): void
    {
        $this->createPlane();
    }

    private function createPlane(?Material $material = null, ?Matrix $transform = null): void
    {
        $newPlane = ShapeFactory::createPlane();
        if ($material) {
            $newPlane->setMaterial($material);
        }

        if ($transform) {
            $newPlane->setTransform($transform);
        }

        if (! isset($this->shapeContext->shapeA)) {
            $this->shapeContext->shapeA = $newPlane;

            return;
        }

        if (! isset($this->shapeContext->shapeB)) {
            $this->shapeContext->shapeB = $newPlane;

            return;
        }

        throw new LogicException('No Plane is set.');
    }

    /** @When /^(n1|n2|n3) is a local_normal_at\((p), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function nIsALocalNormalAtPoint(string $expression1, string $expression2, float $x, float $y, float $z): void
    {
    }

    /** @Given /^(n1|n2|n3) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function nIsAVector(string $expression, float $x, float $y, float $z): void
    {
        $actual = $this->shapeContext->shapeA->normalAt(TupleFactory::createPoint($x, $y, $z));
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($actual));
    }

    /** @When /^(lxs) is a local_intersect\((p), (r)\)$/ */
    public function xsIsALocalIntersectPR(): void
    {
        $this->localIntersections = $this->shapeContext->shapeA->intersect($this->rayContext->rayA);
    }

    /** @Then /^(lxs) is empty$/ */
    public function xsIsEmpty(): void
    {
        Assert::assertEmpty($this->localIntersections);
    }

    /** @Then /^(lxs)\.count = (\d+)$/ */
    public function intersectionCount(string $expression, int $count): void
    {
        Assert::assertCount($count, $this->localIntersections);
    }

    /** @Given /^(lxs)\[(\d+)\]\.t = ([-+]?\d*\.?\d+)$/ */
    public function intersectionObjectAtIndexIsT(string $expression, int $index, float $value): void
    {
        Assert::assertSame($value, $this->localIntersections[$index]->getT());
    }

    /** @Given /^(lxs)\[(\d+)\]\.object = (p)$/ */
    public function intersectionObjectAtIndexIsObject(string $expression, int $index): void
    {
        Assert::assertSame($this->shapeContext->shapeA, $this->localIntersections[$index]->getObject());
    }
}
