<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Light\LightFactory;
use PHPUnit\Framework\Assert;

final class LightContext implements Context
{
    private Light $light;

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
        Assert::assertTrue($this->light->position->isEqualTo($this->tupleContext->tupleA));
    }

    /** @Given /^(light)\.intensity = (intensity)$/ */
    public function lightIntensityIsIntensity(): void
    {
        Assert::assertTrue($this->light->intensity->isEqualTo($this->colorContext->colorA));
    }
}
