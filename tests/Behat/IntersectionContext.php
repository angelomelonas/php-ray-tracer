<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use _PHPStan_7c8075089\Symfony\Component\Console\Exception\LogicException;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Intersection\IntersectionFactory;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Sphere\Sphere;
use PHPUnit\Framework\Assert;

final class IntersectionContext implements Context
{
    private Intersection $intersectionA;
    private Intersection $intersectionB;
    private Intersection $intersectionC;
    private Intersection $intersectionD;

    private Intersections $intersections;

    private ?Intersection $hit = null;

    private SphereContext $sphereContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->sphereContext = $environment->getContext(SphereContext::class);
    }

    /** @When /^([^"]+) is a intersection\(([-+]?\d*\.?\d+), (s)\)$/ */
    public function iIsAIntersectionOfSphereS(string $expression, float $t): void
    {
        $this->createIntersection($t, $this->sphereContext->sphere);
    }

    /** @Then /^(i)\.t = ([-+]?\d*\.?\d+)$/ */
    public function tValueOfIntersectionIs(string $expression, float $value): void
    {
        Assert::assertSame($value, $this->intersectionA->getT());
    }

    /** @Given /^(i)\.object = (s)$/ */
    public function intersectionObjectIsSphere(): void
    {
        Assert::assertSame($this->sphereContext->sphere, $this->intersectionA->getObject());
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

    private function createIntersection(float $t, Sphere $sphere): Intersection
    {
        if (! isset($this->intersectionA)) {
            $this->intersectionA = IntersectionFactory::create($t, $sphere);

            return $this->intersectionA;
        }

        if (! isset($this->intersectionB)) {
            $this->intersectionB = IntersectionFactory::create($t, $sphere);

            return $this->intersectionB;
        }

        if (! isset($this->intersectionC)) {
            $this->intersectionC = IntersectionFactory::create($t, $sphere);

            return $this->intersectionC;
        }

        if (! isset($this->intersectionD)) {
            $this->intersectionD = IntersectionFactory::create($t, $sphere);

            return $this->intersectionD;
        }

        throw new LogicException('No intersection is set.');
    }
}
