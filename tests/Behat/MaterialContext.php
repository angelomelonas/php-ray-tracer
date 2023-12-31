<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\Tests\Behat\Utility\TestShape;
use PHPUnit\Framework\Assert;

final class MaterialContext implements Context
{
    public Material $material;
    private Color $colorResult;
    private bool $inShadow = false;

    private TupleContext $tupleContext;
    private LightContext $lightContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
        /* @phpstan-ignore-next-line */
        $this->lightContext = $environment->getContext(LightContext::class);
    }

    /** @Given /^(m) is a material\(\)$/ */
    public function mIsAMaterial(): void
    {
        $this->material = new Material();
    }

    /** @Then /^(m)\.color = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function mColorColor(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(ColorFactory::create($x, $y, $z)->isEqualTo($this->material->getColor()));
    }

    /** @Given /^(m)\.ambient = ([-+]?\d*\.?\d+)$/ */
    public function mAmbient(string $expression, float $value): void
    {
        Assert::assertEquals($value, $this->material->getAmbient());
    }

    /** @Given /^(m)\.diffuse = ([-+]?\d*\.?\d+)$/ */
    public function mDiffuse(string $expression, float $value): void
    {
        Assert::assertEquals($value, $this->material->getDiffuse());
    }

    /** @Given /^(m)\.specular = ([-+]?\d*\.?\d+)$/ */
    public function mSpecular(string $expression, float $value): void
    {
        Assert::assertEquals($value, $this->material->getSpecular());
    }

    /** @Given /^in_shadow is (true$)/ */
    public function inShadowIsTrue(): void
    {
        $this->inShadow = true;
    }

    /** @Given /^(m)\.shininess = ([-+]?\d*\.?\d+)$/ */
    public function mShininess(string $expression, float $value): void
    {
        Assert::assertEquals($value, $this->material->getShininess());
    }

    /** @When /^(result) is a lighting\((m), (light), (position), (eyev), (normalv)\)$/ */
    public function resultIsALightingMLightPositionEyevNormalv(): void
    {
        $this->colorResult = $this->material->lighting(
            new TestShape(),
            $this->lightContext->light,
            $this->tupleContext->tupleA,
            $this->tupleContext->tupleB,
            $this->tupleContext->tupleC,
            $this->inShadow
        );
    }

    /** @When /^(result) is a lighting\((m), (light), (position), (eyev), (normalv), (in_shadow)\)$/ */
    public function resultIsALightingMLightPositionEyevNormalvInShadow(): void
    {
        $this->colorResult = $this->material->lighting(
            new TestShape(),
            $this->lightContext->light,
            $this->tupleContext->tupleA,
            $this->tupleContext->tupleB,
            $this->tupleContext->tupleC,
            $this->inShadow
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
        Assert::assertEquals($value, $this->material->getReflective());
    }

    /** @Then /^(m)\.transparency = ([-+]?\d*\.?\d+)$/ */
    public function mTransparency(string $expression, float $value): void
    {
        $this->material->setTransparency($value);
    }

    /** @Given /^(m)\.refractive_index = ([-+]?\d*\.?\d+)$/ */
    public function mRefractiveIndex(string $expression, float $value): void
    {
        $this->material->setRefractiveIndex($value);
    }
}
