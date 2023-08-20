<?php

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use LogicException;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;

final class TupleContext implements Context
{
    public Tuple $tupleA;
    public Tuple $tupleB;
    public Tuple $tupleC;
    public Tuple $tupleD;

    /**
     * @Given /^([^"]+) is a tuple\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function aIsATuple(string $expression, float $x, float $y, float $z, float $w): void
    {
        $this->createTuple($x, $y, $z, $w);
    }

    /**
     * @Then /^([^"]+)\.(x|y|z|w) = ([-+]?\d+\.\d+)$/
     */
    public function aIsEqualTo(string $expression, string $property, float $solution): void
    {
        Assert::assertSame($solution, $this->tupleA->$property);
    }

    /**
     * @Given /^([^"]+) is a point$/
     */
    public function aIsAPoint(): void
    {
        Assert::assertSame(1.0, $this->tupleA->w);
        Assert::assertTrue($this->tupleA->isPoint());
    }

    /**
     * @Given /^([^"]+) is a vector$/
     */
    public function aIsAVector(): void
    {
        Assert::assertSame(0.0, $this->tupleA->w);
        Assert::assertTrue($this->tupleA->isVector());
    }

    /**
     * @Given /^([^"]+) is not a vector$/
     */
    public function aIsNotAVector(): void
    {
        Assert::assertNotSame(0.0, $this->tupleA->w);
        Assert::assertNotTrue($this->tupleA->isVector());
    }

    /**
     * @Given /^([^"]+) is not a point$/
     */
    public function aIsNotAPoint(): void
    {
        Assert::assertSame(0.0, $this->tupleA->w);
        Assert::assertNotTrue($this->tupleA->isPoint());
    }

    /**
     * @Given /^([^"]+) is a point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function pIsAPoint(string $expression, float $x, float $y, float $z): void
    {
        $this->createTuple($x, $y, $z, Tuple::POINT);
    }

    /**
     * @Given /^([^"]+) is a vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function vIsAVector(string $expression, float $x, float $y, float $z): void
    {
        $this->createTuple($x, $y, $z, Tuple::VECTOR);
    }

    /**
     * @Then /^(p|v) = tuple\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function pIsATuple(string $expression, float $x, float $y, float $z, float $w): void
    {
        Assert::assertTrue($this->tupleA->isEqualTo(TupleFactory::create($x, $y, $z, $w)));
    }

    /**
     * @Then /^(([^"]+) \+ ([^"]+)) = tuple\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function a1PlusA2IsATuple(string $expression, string $a1, string $a2, float $x, float $y, float $z, float $w): void
    {
        $result = $this->tupleA->add($this->tupleB);

        Assert::assertTrue($result->isEqualTo(TupleFactory::create($x, $y, $z, $w)));
    }

    /**
     * @Then /^(([^"]+) \- ([^"]+)) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function a1MinusA2IsAVector(string $expression, string $a1, string $a2, float $x, float $y, float $z): void
    {
        $result = $this->tupleA->subtract($this->tupleB);
        Assert::assertTrue($result->isEqualTo(TupleFactory::createVector($x, $y, $z)));
    }

    /**
     * @Then /^(([^"]+) \- ([^"]+)) = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function pMinusVIsAPoint(string $expression, string $a1, string $a2, float $x, float $y, float $z): void
    {
        $result = $this->tupleA->subtract($this->tupleB);
        Assert::assertTrue($result->isEqualTo(TupleFactory::createPoint($x, $y, $z)));
    }

    /**
     * @Then /^\-([^"]+) = tuple\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function aIsANegatedTuple(string $a, float $x, float $y, float $z, float $w): void
    {
        $result = $this->tupleA->negate();
        Assert::assertTrue(TupleFactory::create($x, $y, $z, $w)->isEqualTo($result));
    }

    /**
     * @Then /^a \* ([-+]?\d*\.?\d+) = tuple\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function aTupleIsMultipliedByAScalar(float $scalar, float $x, float $y, float $z, float $w): void
    {
        $result = $this->tupleA->multiply($scalar);
        Assert::assertTrue(TupleFactory::create($x, $y, $z, $w)->isEqualTo($result));
    }

    /**
     * @Then /^a \/ ([-+]?\d*\.?\d+) = tuple\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function aTupleIsDividedByAScalar(float $scalar, float $x, float $y, float $z, float $w): void
    {
        $result = $this->tupleA->divide($scalar);
        Assert::assertTrue(TupleFactory::create($x, $y, $z, $w)->isEqualTo($result));
    }

    /**
     * @Then /^magnitude\(([^"]+)\) = ([-+]?\d*\.?\d+)$/
     */
    public function magnitudeOfVIs(string $expression, float $solution): void
    {
        $result = $this->tupleA->magnitude();
        Assert::assertSame($solution, $result);
    }

    /**
     * @Then /^magnitude\(([^"]+)\) = √([-+]?\d*\.?\d+)$/
     */
    public function magnitudeOfVIsRoot(string $expression, float $solution): void
    {
        $result = $this->tupleA->magnitude();
        Assert::assertSame(sqrt($solution), $result);
    }

    /**
     * @Then /^normalize\(([^"]+)\) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function normalizeOfVIs(string $expression, float $x, float $y, float $z): void
    {
        $result = $this->tupleA->normalize();
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($result));
    }

    /**
     * @Then /^normalize\(([^"]+)\) = vector\(1\/√([-+]?\d*\.?\d+), 2\/√([-+]?\d*\.?\d+), 3\/√([-+]?\d*\.?\d+)\)$/
     */
    public function normalizeOfVIsRoot(string $expression, float $x, float $y, float $z): void
    {
        $result = $this->tupleA->normalize();
        Assert::assertTrue(TupleFactory::createVector(1 / sqrt($x), 2 / sqrt($y), 3 / sqrt($z))->isEqualTo($result));
    }

    /**
     * @When /^norm is a normalize\(([^"]+)\)$/
     */
    public function normIsANormalizeV(): void
    {
        $this->tupleA = $this->tupleA->normalize();
    }

    /**
     * @Then /^dot\(([^"]+), ([^"]+)\) = ([-+]?\d*\.?\d+)$/
     */
    public function dotProductOfAAndBIs(string $a, string $b, float $solution): void
    {
        $result = $this->tupleA->dot($this->tupleB);
        Assert::assertSame($solution, $result);
    }

    /**
     * @Then /^cross\(a, b\) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function crossProductOfAAndBIs(float $x, float $y, float $z): void
    {
        $result = $this->tupleA->cross($this->tupleB);
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($result));
    }

    /**
     * @Then /^cross\(b, a\) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function crossProductOfBAndAIs(float $x, float $y, float $z): void
    {
        $result = $this->tupleB->cross($this->tupleA);
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($result));
    }

    private function createTuple(float $x, float $y, float $z, float $w): void
    {
        if (!isset($this->tupleA)) {
            $this->tupleA = TupleFactory::create($x, $y, $z, $w);

            return;
        }

        if (!isset($this->tupleB)) {
            $this->tupleB = TupleFactory::create($x, $y, $z, $w);

            return;
        }

        if (!isset($this->tupleC)) {
            $this->tupleC = TupleFactory::create($x, $y, $z, $w);

            return;
        }

        if (!isset($this->tupleD)) {
            $this->tupleD = TupleFactory::create($x, $y, $z, $w);

            return;
        }

        throw new LogicException('No tuple is set.');
    }
}