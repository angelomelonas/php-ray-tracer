<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Light\LightFactory;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;

final class MaterialContext implements Context
{
    public Material $material;
    public Light $light;
    private Color $colorResult;

    private TupleContext $tupleContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
    }

    /** @Given /^(m) is a material\(\)$/ */
    public function mIsAMaterial(): void
    {
        $this->material = new Material();
    }

    /** @Then /^(m)\.color = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function mColorColor(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(ColorFactory::create($x, $y, $z)->isEqualTo($this->material->color));
    }

    /** @Given /^(m)\.ambient = ([-+]?\d*\.?\d+)$/ */
    public function mAmbient(string $expression, float $value): void
    {
        Assert::assertEquals($value, $this->material->ambient);
    }

    /** @Given /^(m)\.diffuse = ([-+]?\d*\.?\d+)$/ */
    public function mDiffuse(string $expression, float $value): void
    {
        Assert::assertEquals($value, $this->material->diffuse);
    }

    /** @Given /^(m)\.specular = ([-+]?\d*\.?\d+)$/ */
    public function mSpecular(string $expression, float $value): void
    {
        Assert::assertEquals($value, $this->material->specular);
    }

    /** @Given /^(m)\.shininess = ([-+]?\d*\.?\d+)$/ */
    public function mShininess(string $expression, float $value): void
    {
        Assert::assertEquals($value, $this->material->shininess);
    }

    /** @Given /^(light) is a point_light\(point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\), color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function lightIsAPointLightAtPointWithColor(string $expression, float $x, float $y, float $z, float $red, float $green, float $blue): void
    {
        $this->light = LightFactory::create(TupleFactory::createPoint($x, $y, $z), ColorFactory::create($red, $green, $blue));
    }

    /** @When /^(result) is a lighting\((m), (light), (position), (eyev), (normalv)\)$/ */
    public function resultIsALightingMLightPositionEyevNormalv(): void
    {
        $this->colorResult = $this->material->lighting(
            $this->light,
            $this->tupleContext->tupleA,
            $this->tupleContext->tupleB,
            $this->tupleContext->tupleC
        );
    }

    /** @Then /^(result) = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function resultIsColor(string $expression, float $red, float $green, float $blue): void
    {
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($this->colorResult));
    }

    /** @Then /^(m)\.reflective = ([-+]?\d*\.?\d+)$/ */
    public function mReflective(string $expression, float $value): void
    {
        throw new PendingException();
    }
}
