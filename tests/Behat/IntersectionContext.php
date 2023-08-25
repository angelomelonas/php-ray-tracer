<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use LogicException;
use PhpRayTracer\RayTracer\Intersection\Computation;
use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Intersection\IntersectionFactory;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\Utility\Utility;
use PHPUnit\Framework\Assert;
use function filter_var;
use const FILTER_VALIDATE_BOOLEAN;

final class IntersectionContext implements Context
{
    private Intersection $intersectionA;
    private Intersection $intersectionB;
    private Intersection $intersectionC;
    private Intersection $intersectionD;
    public Intersections $intersections;
    private ?Intersection $hit = null;
    public Computation $computation;

    private SphereContext $sphereContext;
    private RayContext $rayContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->sphereContext = $environment->getContext(SphereContext::class);
        /* @phpstan-ignore-next-line */
        $this->rayContext = $environment->getContext(RayContext::class);
    }

    /** @When /^([^"]+) is a intersection\(([-+]?\d*\.?\d+), (s|s1|shape)\)$/ */
    public function iIsAIntersectionOfSphereS1(string $expression, float $t): void
    {
        $this->createIntersection($t, $this->sphereContext->sphereA);
    }

    /** @When /^([^"]+) is a intersection\(([-+]?\d*\.?\d+), (s2)\)$/ */
    public function iIsAIntersectionOfSphereS2(string $expression, float $t): void
    {
        $this->createIntersection($t, $this->sphereContext->sphereB);
    }

    /** @Then /^(i)\.t = ([-+]?\d*\.?\d+)$/ */
    public function tValueOfIntersectionIs(string $expression, float $value): void
    {
        Assert::assertSame($value, $this->intersectionA->getT());
    }

    /** @Given /^(i)\.object = (s)$/ */
    public function intersectionObjectIsSphere(): void
    {
        Assert::assertSame($this->sphereContext->sphereA, $this->intersectionA->getObject());
    }

    /** @Given /^(xs) is a intersections\((i2), (i1)\)$/ */
    public function xsIsACollectionOfIntersections(): void
    {
        $this->intersections = new Intersections([$this->intersectionA, $this->intersectionB]);
    }

    /** @When /^(i) is a hit\((xs)\)$/ */
    public function intersectionIsAHitInIntersections(): void
    {
        $this->hit = $this->intersections->hit();
    }

    /** @Then /^(i) = (i1)$/ */
    public function intersectionIsEqualToI1(): void
    {
        Assert::assertSame($this->intersectionA, $this->hit);
    }

    /** @Then /^(i) = (i2)$/ */
    public function intersectionIsEqualToI2(): void
    {
        Assert::assertSame($this->intersectionB, $this->hit);
    }

    /** @Then /^(i) = (i3)$/ */
    public function intersectionIsEqualToI3(): void
    {
        Assert::assertSame($this->intersectionC, $this->hit);
    }

    /** @Then /^(i) = (i4)$/ */
    public function intersectionIsEqualToI4(): void
    {
        Assert::assertSame($this->intersectionD, $this->hit);
    }

    /** @Then /^(i) is nothing$/ */
    public function hitIsNull(): void
    {
        Assert::assertNull($this->hit);
    }

    /** @Then /^(xs)\.count = (\d+)$/ */
    public function intersectionCount(string $expression, int $count): void
    {
        Assert::assertCount($count, $this->intersections);
    }

    /** @Given /^(xs)\[(\d+)\] = ([-+]?\d*\.?\d+)$/ */
    public function intersectionAtIndexIs(string $expression, int $index, float $value): void
    {
        Assert::assertSame($value, $this->intersections->get($index)->getT());
    }

    /** @Given /^(xs)\[(\d+)\]\.t = ([-+]?\d*\.?\d+)$/ */
    public function intersectionObjectAtIndexIsT(string $expression, int $index, float $value): void
    {
        Assert::assertSame($value, $this->intersections->get($index)->getT());
    }

    /** @Given /^xs is a intersections\((i1), (i2), (i3), (i4)\)$/ */
    public function xsIsAIntersectionsI1I2I3I4(): void
    {
        $this->intersections = new Intersections([
            $this->intersectionA,
            $this->intersectionB,
            $this->intersectionC,
            $this->intersectionD,
        ]);
    }

    public function createIntersection(float $t, Shape $shape): Intersection
    {
        if (! isset($this->intersectionA)) {
            $this->intersectionA = IntersectionFactory::create($t, $shape);

            return $this->intersectionA;
        }

        if (! isset($this->intersectionB)) {
            $this->intersectionB = IntersectionFactory::create($t, $shape);

            return $this->intersectionB;
        }

        if (! isset($this->intersectionC)) {
            $this->intersectionC = IntersectionFactory::create($t, $shape);

            return $this->intersectionC;
        }

        if (! isset($this->intersectionD)) {
            $this->intersectionD = IntersectionFactory::create($t, $shape);

            return $this->intersectionD;
        }

        throw new LogicException('No intersection is set.');
    }

    /** @When /^(comps) is a prepare_computations\((i), (r)\)$/ */
    public function compsIsAPrepareComputations(): void
    {
        $this->computation = $this->intersectionA->prepareComputations($this->rayContext->rayA);
    }

    /** @Then /^(comps)\.t = (i)\.t$/ */
    public function compsTIsT(): void
    {
        Assert::assertEquals($this->intersectionA->getT(), $this->computation->getT());
    }

    /** @Given /^(comps)\.object = i\.object$/ */
    public function compsObjectIObject(): void
    {
        Assert::assertSame($this->intersectionA->getObject(), $this->computation->getObject());
    }

    /** @Given /^(comps)\.point = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function compsPointPoint1(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($this->computation->getPoint()));
    }

    /** @Given /^(comps)\.eyev = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function compsEyevVector1(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($this->computation->getEyeVector()));
    }

    /** @Given /^(comps)\.normalv = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function compsNormalvVector1(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($this->computation->getNormalVector()));
    }

    /** @Then /^(comps)\.inside = (true|false)$/ */
    public function compsInsideTrueOrFalse(string $expression, string $bool): void
    {
        Assert::assertEquals(filter_var($bool, FILTER_VALIDATE_BOOLEAN), $this->computation->isInside());
    }

    /** @Then /^(comps)\.over_point\.z < (\-EPSILON\/2)$/ */
    public function compsOverPointZEPSILON(): void
    {
        Assert::assertLessThan(Utility::PRECISION_TEST, $this->computation->getOverPoint()->z);
    }

    /** @Given /^(comps)\.point\.z > (comps)\.over_point\.z$/ */
    public function compsPointZCompsOverPointZ(): void
    {
        $result = Utility::isFloatGreaterThanFloat($this->computation->getPoint()->z, $this->computation->getOverPoint()->z);
        Assert::assertTrue($result);
    }
}
