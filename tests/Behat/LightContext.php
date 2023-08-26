<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Light\LightFactory;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;

final class LightContext implements Context
{
    public Light $light;

    private ColorContext $colorContext;
    private TupleContext $tupleContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->colorContext = $environment->getContext(ColorContext::class);
        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
    }

    /** @When /^(light) is a point_light\((position), (intensity)\)$/ */
    public function lightIsAPointLightWithPositionAndIntensity(): void
    {
        $this->light = LightFactory::create($this->tupleContext->tupleA, $this->colorContext->colorA);
    }

    /** @Then /^(light)\.position = (position)$/ */
    public function lightPositionIsPosition(): void
    {
        Assert::assertTrue($this->light->getPosition()->isEqualTo($this->tupleContext->tupleA));
    }

    /** @Given /^(light)\.intensity = (intensity)$/ */
    public function lightIntensityIsIntensity(): void
    {
        Assert::assertTrue($this->light->getIntensity()->isEqualTo($this->colorContext->colorA));
    }

    /** @Given /^(light) is a point_light\(point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\), color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function lightIsAPointLightAtPointWithColor(string $expression, float $x, float $y, float $z, float $red, float $green, float $blue): void
    {
        $this->light = LightFactory::create(TupleFactory::createPoint($x, $y, $z), ColorFactory::create($red, $green, $blue));
    }
}
