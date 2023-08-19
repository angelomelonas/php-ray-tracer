<?php

namespace PhpRayTracer\Tests\Behat;

use _PHPStan_7c8075089\Symfony\Component\Console\Exception\LogicException;
use Behat\Behat\Context\Context;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;

final class ColorContext implements Context
{
    private Color $colorA;
    private Color $colorB;

    /**
     * @Given /^([^"]+) is a color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function cIsAColor(string $expression, float $red, float $green, float $blue): void
    {
        $this->createColor($red, $green, $blue);
    }

    /**
     * @Then /^([^"]+)\.(red|green|blue) = ([-+]?\d+\.\d+)$/
     */
    public function cRedIsEqualTo(string $expression, string $property, float $solution): void
    {
        Assert::assertSame($solution, $this->colorA->$property);
    }
    
    /**
     * @Then /^([^"]+) \+ ([^"]+) = color\(([-+]?\d+\.\d+), ([-+]?\d+\.\d+), ([-+]?\d+\.\d+)\)$/
     */
    public function c1PlusC2IsAColor(string $c1, string $c2, float $red, float $green, float $blue): void
    {
        $result = $this->colorA->add($this->colorB);
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($result));
    }

    /**
     * @Then /^([^"]+) \- ([^"]+) = color\(([-+]?\d+\.\d+), ([-+]?\d+\.\d+), ([-+]?\d+\.\d+)\)$/
     */
    public function c1MinusC2IsAColor(string $c1, string $c2, float $red, float $green, float $blue): void
    {
        $result = $this->colorA->subtract($this->colorB);
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($result));
    }

    /**
     * @Then /^c \* ([-+]?\d*\.?\d+) = color\(([-+]?\d+\.\d+), ([-+]?\d+\.\d+), ([-+]?\d+\.\d+)\)$/
     */
    public function cMultipliedByAScalar(float $scalar, float $x, float $y, float $z): void
    {
        $result = $this->colorA->multiply($scalar);
        Assert::assertTrue(ColorFactory::create($x, $y, $z)->isEqualTo($result));
    }

    /**
     * @Then /^c([^"]+) \* c([^"]+) = color\(([-+]?\d+\.\d+), ([-+]?\d+\.\d+), ([-+]?\d+\.\d+)\)$/
     */
    public function c1MultipliedByC2IsAColor(string $c1, string $c2, float $red, float $green, float $blue): void
    {
        $result = $this->colorA->hadamardProduct($this->colorB);
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($result));
    }

    private function createColor(float $red, float $green, float $blue): void
    {
        if (!isset($this->colorA)) {
            $this->colorA = ColorFactory::create($red, $green, $blue);

            return;
        }

        if (!isset($this->colorB)) {
            $this->colorB = ColorFactory::create($red, $green, $blue);

            return;
        }

        throw new LogicException('No color is set.');
    }
}