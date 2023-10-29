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
use function sqrt;
use const FILTER_VALIDATE_BOOLEAN;
use const PHP_FLOAT_EPSILON;

final class IntersectionContext implements Context
{
    private Intersection $intersectionA;
    private Intersection $intersectionB;
    private Intersection $intersectionC;
    private Intersection $intersectionD;
    private Intersection $intersectionE;
    private Intersection $intersectionF;
    public Intersections $intersections;
    private ?Intersection $hit = null;
    public Computation $computation;

    private ShapeContext $shapeContext;
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

    /** @When /^([^"]+) is a intersection\(([-+]?\d*\.?\d+), (s|s1|shape)\)$/ */
    public function iIsAIntersectionOfSphereS1(string $expression, float $t): void
    {
        $this->createIntersection($t, $this->shapeContext->shapeA);
    }

    /** @When /^([^"]+) is a intersection\(([-+]?\d*\.?\d+), (s2)\)$/ */
    public function iIsAIntersectionOfSphere(string $expression, float $t): void
    {
        $this->createIntersection($t, $this->shapeContext->shapeB);
    }

    /** @Given /^(i) is a intersection\((√2), (shape)\)$/ */
    public function iIsAIntersectionShape(): void
    {
        $this->createIntersection(sqrt(2), $this->shapeContext->shapeA);
    }

    /** @Given /^(i) is a intersection\((√2), (plane)\)$/ */
    public function iIsAIntersectionPlane(): void
    {
        $this->createIntersection(sqrt(2), $this->shapeContext->shapeC);
    }

    /** @Then /^(i)\.t = ([-+]?\d*\.?\d+)$/ */
    public function tValueOfIntersectionIs(string $expression, float $value): void
    {
        Assert::assertSame($value, $this->intersectionA->getT());
    }

    /** @Given /^(i)\.object = (s)$/ */
    public function intersectionObjectIsSphere(): void
    {
        Assert::assertSame($this->shapeContext->shapeA, $this->intersectionA->getShape());
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

    /** @When /^(comps) is a prepare_computations\((i), (r)\)$/ */
    public function compsIsAPrepareComputations(): void
    {
        $this->computation = $this->intersectionA->prepareComputations(
            $this->rayContext->rayA,
            new Intersections([$this->intersectionA])
        );
    }

    /** @Then /^(comps)\.t = (i)\.t$/ */
    public function compsTIsT(): void
    {
        Assert::assertEquals($this->intersectionA->getT(), $this->computation->getT());
    }

    /** @Given /^(comps)\.object = i\.object$/ */
    public function compsObjectIObject(): void
    {
        Assert::assertSame($this->intersectionA->getShape(), $this->computation->getShape());
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

    /** @Then /^(comps)\.reflectv = vector\((0), (√2\/2), (√2\/2)\)$/ */
    public function compsReflectVector(): void
    {
        Assert::assertTrue(TupleFactory::createVector(0, sqrt(2) / 2, sqrt(2) / 2)->isEqualTo($this->computation->getReflectVector()));
    }

    /** @Given /^(xs) is a intersections\((2:A), (2.75:B), (3.25:C), (4.75:B), (5.25:C), (6:A)\)$/ */
    public function xsIsAIntersections(): void
    {
        $this->intersections = new Intersections([
            $this->createIntersection(2, $this->shapeContext->shapeA),
            $this->createIntersection(2.75, $this->shapeContext->shapeB),
            $this->createIntersection(3.25, $this->shapeContext->shapeC),
            $this->createIntersection(4.75, $this->shapeContext->shapeB),
            $this->createIntersection(5.25, $this->shapeContext->shapeC),
            $this->createIntersection(6, $this->shapeContext->shapeA),
        ]);
    }

    /** @Given /^(xs) is a intersections\((i)\)$/ */
    public function xsIsAIntersectionsI(): void
    {
        $this->intersections = new Intersections([$this->intersectionA]);
    }

    /** @Given /^(xs) is a intersections\(([-+]?\d*\.?\d+):shape, ([-+]?\d*\.?\d+):shape\)$/ */
    public function xsIsAIntersectionWithShape(string $expression, float $a, float $b): void
    {
        $this->intersections = new Intersections([
            $this->createIntersection($a, $this->shapeContext->shapeA),
            $this->createIntersection($b, $this->shapeContext->shapeA),
        ]);
    }

    /** @Given /^(xs) is a intersections\((-√2\/2):(shape), (√2\/2):(shape)\)$/ */
    public function xsIsAIntersections22Shape22Shape(): void
    {
        $this->intersections = new Intersections([
            $this->createIntersection(-sqrt(2)/2, $this->shapeContext->shapeA),
            $this->createIntersection(sqrt(2)/2, $this->shapeContext->shapeA),
        ]);
    }

    /** @Given /^(xs) is a intersections\(([-+]?\d*\.?\d+):(A), ([-+]?\d*\.?\d+):(B), ([-+]?\d*\.?\d+):(B), ([-+]?\d*\.?\d+):(A)\)$/ */
    public function xsIsAIntersections9899A4899B4899B9899A(): void
    {
        $this->intersections = new Intersections([
            $this->createIntersection(-0.9899, $this->shapeContext->shapeA),
            $this->createIntersection(-0.4899, $this->shapeContext->shapeB),
            $this->createIntersection(0.4899, $this->shapeContext->shapeB),
            $this->createIntersection(0.9899, $this->shapeContext->shapeA),
        ]);
    }

    /** @Given /^(xs) is a intersections\((√2):(floor)\)$/ */
    public function xsIsAIntersections2Floor(): void
    {
        $this->intersections = new Intersections([
            $this->createIntersection(sqrt(2), $this->shapeContext->shapeC),
        ]);
    }

    /** @Given /^(xs) is a intersections\(([-+]?\d*\.?\d+):(shape)\)$/ */
    public function xsIsAIntersectionsShape(): void
    {
        $this->intersections = new Intersections([
            $this->createIntersection(1.8589, $this->shapeContext->shapeA),
        ]);
    }

    /** @When /^(comps) is a prepare_computations\((xs)\[(.*)\], (r), (xs)\)$/ */
    public function compsIsAPrepareComputationsXsRXs(string $expression1, string $expression2, int $index): void
    {
        $this->computation = $this->intersections
            ->get($index)
            ->prepareComputations(
                $this->rayContext->rayA,
                $this->intersections
            );
    }

    /** @When /^(comps) is a prepare_computations\((i), (r), (xs)\)$/ */
    public function compsIsAPrepareComputationsI(): void
    {
        $this->computation = $this->intersectionA
            ->prepareComputations(
                $this->rayContext->rayA,
                $this->intersections
            );
    }

    /** @Then /^(comps)\.(n1) = (.*)$/ */
    public function compsN1(string $expression1, string $expression2, float $value): void
    {
        Assert::assertEquals($value, $this->computation->getN1());
    }

    /** @Then /^(comps)\.(n2) = (.*)$/ */
    public function compsN2(string $expression1, string $expression2, float $value): void
    {
        Assert::assertEquals($value, $this->computation->getN2());
    }

    /** @Then /^(comps)\.under_point\.z > EPSILON\/2$/ */
    public function compsUnderPointZEPSILON(): void
    {
        Assert::assertGreaterThan(PHP_FLOAT_EPSILON/2, $this->computation->getUnderPoint()->z);
    }

    /** @Given /^(comps)\.point\.z < comps\.under_point\.z$/ */
    public function compsPointZCompsUnderPointZ(): void
    {
        Assert::assertLessThan($this->computation->getUnderPoint()->z, $this->computation->getPoint()->z);
    }

    /** @Given /^(reflectance) is a schlick\((comps)\)$/ */
    public function reflectanceIsASchlickComps(): void
    {
    }

    /** @Then /^(reflectance) = ([-+]?\d*\.?\d+)$/ */
    public function reflectance(string $expression, float $value): void
    {
        Assert::assertEqualsWithDelta($value, $this->computation->schlick(), Utility::PRECISION_TEST);
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

        if (! isset($this->intersectionE)) {
            $this->intersectionE = IntersectionFactory::create($t, $shape);

            return $this->intersectionE;
        }

        if (! isset($this->intersectionF)) {
            $this->intersectionF = IntersectionFactory::create($t, $shape);

            return $this->intersectionF;
        }

        throw new LogicException('No intersection is set.');
    }
}
