<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use LogicException;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Ray\RayFactory;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;

final class RayContext implements Context
{
    public Ray $rayA;
    public Ray $rayB;
    private Matrix $transformationMatrix;
    private TupleContext $tupleContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
    }

    /** @When /^(r) is a ray\((origin), (direction)\)$/ */
    public function rIsARayOriginDirection(): void
    {
        $this->createRay($this->tupleContext->tupleA, $this->tupleContext->tupleB);
    }

    /** @Then /^(r)\.origin = (origin)$/ */
    public function rOriginOrigin(): void
    {
        Assert::assertSame($this->tupleContext->tupleA, $this->rayA->origin);
    }

    /** @Given /^(r)\.direction = (direction)$/ */
    public function rDirectionDirection(): void
    {
        Assert::assertTrue($this->tupleContext->tupleB->isEqualTo($this->rayA->direction));
    }

    /** @Given /^(r) is a ray\(point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\), vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function rIsARayWithAOriginAndDirection(string $expression, float $x1, float $y1, float $z1, float $x2, float $y2, float $z2): void
    {
        $point = TupleFactory::createPoint($x1, $y1, $z1);
        $vector = TupleFactory::createVector($x2, $y2, $z2);

        $this->createRay($point, $vector);
    }

    /** @Then /^position\((r), ([-+]?\d*\.?\d+)\) = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function positionIsEqualToPoint(string $r, float $t, float $x, float $y, float $z): void
    {
        $expected = $this->rayA->position($t);
        $actual = TupleFactory::createPoint($x, $y, $z);
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /** @When /^(r2) is a transform\((r), (m)\)$/ */
    public function risATransformRM(): void
    {
        $this->rayB = $this->rayA->transform($this->transformationMatrix);
    }

    /** @Then /^(r2)\.origin = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function rOriginIsPoint(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($this->rayB->origin));
    }

    /** @Given /^(r2)\.direction = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function rDirectionIsVector(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($this->rayB->direction));
    }

    /** @Given /^(m) is a translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformIsATranslation(string $expression, float $x, float $y, float $z): void
    {
        $this->transformationMatrix = MatrixFactory::createTranslation($x, $y, $z);
    }

    /** @Given /^(m) is a scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformIsAScaling(string $expression, float $x, float $y, float $z): void
    {
        $this->transformationMatrix = MatrixFactory::createScaling($x, $y, $z);
    }

    private function createRay(Tuple $origin, Tuple $direction): Ray
    {
        if (! isset($this->rayA)) {
            $this->rayA = RayFactory::create($origin, $direction);

            return $this->rayA;
        }

        if (! isset($this->rayB)) {
            $this->rayB = RayFactory::create($origin, $direction);

            return $this->rayB;
        }

        throw new LogicException('No ray is set.');
    }
}
