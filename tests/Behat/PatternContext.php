<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Pattern\Pattern;
use PhpRayTracer\RayTracer\Pattern\PatternFactory;
use PhpRayTracer\RayTracer\Pattern\StripePattern;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\Tests\Behat\Utility\TestPattern;
use PhpRayTracer\Tests\Behat\Utility\TestShape;
use PHPUnit\Framework\Assert;
use function assert;

final class PatternContext implements Context
{
    public Pattern $patternA;

    private Color $color;

    private ColorContext $colorContext;
    private ShapeContext $shapeContext;
    private SphereContext $sphereContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->colorContext = $environment->getContext(ColorContext::class);
        /* @phpstan-ignore-next-line */
        $this->shapeContext = $environment->getContext(ShapeContext::class);
        /* @phpstan-ignore-next-line */
        $this->sphereContext = $environment->getContext(SphereContext::class);
    }

    /** @Given /^pattern is a stripe_pattern\((white), (black)\)$/ */
    public function patternIsAStripePatternWithWhiteAndBlack(): void
    {
        $this->patternA = PatternFactory::createStripePattern($this->colorContext->colorB, $this->colorContext->colorA);
    }

    /** @Then /^pattern\.(a) = (white$)/ */
    public function patternAIsWhite(): void
    {
        $stripePattern = $this->patternA;
        assert($stripePattern instanceof StripePattern);

        Assert::assertTrue($stripePattern->getColorA()->isEqualTo($this->colorContext->colorB));
    }

    /** @Then /^pattern\.(b) = (black)/ */
    public function patternBIsBlack(): void
    {
        $stripePattern = $this->patternA;
        assert($stripePattern instanceof StripePattern);

        Assert::assertTrue($stripePattern->getColorB()->isEqualTo($this->colorContext->colorA));
    }

    /** @Then /^stripe_at\((pattern), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\) = white$/ */
    public function stripeAtPatternPointWhite(string $expression, float $x, float $y, float $z): void
    {
        $stripePattern = $this->patternA;
        assert($stripePattern instanceof StripePattern);

        $actual = $stripePattern->patternAtShape(new TestShape(), TupleFactory::createPoint($x, $y, $z));
        Assert::assertTrue($this->colorContext->colorB->isEqualTo($actual));
    }

    /** @Then /^stripe_at\((pattern), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\) = black/ */
    public function stripeAtPatternPointBlack(string $expression, float $x, float $y, float $z): void
    {
        $stripePattern = $this->patternA;
        assert($stripePattern instanceof StripePattern);

        $actual = $stripePattern->patternAtShape(new TestShape(), TupleFactory::createPoint($x, $y, $z));
        Assert::assertTrue($this->colorContext->colorA->isEqualTo($actual));
    }

//    public function createPattern(): Pattern
//    {
//        if (! isset($this->patternA)) {
//            $this->patternA = PatternFactory::createStripePattern();
//
//            return $this->intersectionA;
//        }
//
//        throw new LogicException('No Pattern is set.');
//    }

    /** @Given /^(object) is a sphere\(\)$/ */
    public function objectIsASphere(): void
    {
        $this->sphereContext->sIsASphere();
    }

    /** @Given /^set_transform\((object|shape), scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setTransformObjectScaling(string $expression, float $x, float $y, float $z): void
    {
        $this->shapeContext->shapeA->setTransform(MatrixFactory::createScaling($x, $y, $z));
    }

    /** @When /^(c) is a stripe_at_object\((pattern), (object), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function cIsAStripeAtObjectPatternObjectAtPoint(
        string $expression1,
        string $expression2,
        string $expression3,
        float $x,
        float $y,
        float $z,
    ): void {
        $stripePattern = $this->patternA;
        assert($stripePattern instanceof StripePattern);

        $this->color = $stripePattern->patternAtShape($this->shapeContext->shapeA, TupleFactory::createPoint($x, $y, $z));
    }

    /** @Then /^(c) = (white$)/ */
    public function cIsWhite(): void
    {
        Assert::assertTrue(ColorFactory::createWhite()->isEqualTo($this->color));
    }

    /** @Given /^set_pattern_transform\((pattern), scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setPatternTransformPatternScaling(string $expression, float $x, float $y, float $z): void
    {
        $this->patternA->setTransform(MatrixFactory::createScaling($x, $y, $z));
    }

    /** @Given /^set_pattern_transform\((pattern), translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setPatternTransformPatternTranslation(string $expression, float $x, float $y, float $z): void
    {
        $this->patternA->setTransform(MatrixFactory::createTranslation($x, $y, $z));
    }

    /** @Given /^(pattern) is a test_pattern\(\)$/ */
    public function patternIsATestPattern(): void
    {
        $this->patternA = new TestPattern();
    }

    /** @Then /^(pattern)\.transform = (identity_matrix$)/ */
    public function patternTransformIdentityMatrix(): void
    {
        Assert::assertTrue(MatrixFactory::createIdentity()->isEqualTo($this->patternA->getTransform()));
    }

    /** @Then /^(pattern)\.transform = translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function patternTransformTranslation(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(MatrixFactory::createTranslation($x, $y, $z)->isEqualTo($this->patternA->getTransform()));
    }

    /** @When /^(c) is a pattern_at_shape\((pattern), (shape), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function cIsAPatternAtShapePatternShapePoint(
        string $expression1,
        string $expression2,
        string $expression3,
        float $x,
        float $y,
        float $z,
    ): void {
        $this->color = $this->patternA->patternAtShape($this->shapeContext->shapeA, TupleFactory::createPoint($x, $y, $z));
    }

    /** @Then /^(c) = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function cIsAcColor(string $expression, float $red, float $green, float $blue): void
    {
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($this->color));
    }

    /** @Given /^pattern is a gradient_pattern\((white), (black)\)$/ */
    public function patternIsAGradientPatternWhiteBlack(): void
    {
        $this->patternA = PatternFactory::createGradientPattern($this->colorContext->colorB, $this->colorContext->colorA);
    }

    /** @Then /^pattern_at\((pattern), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\) = (white)$/ */
    public function patternAtPatternPointIsWhite(string $expression, float $x, float $y, float $z): void
    {
        $actual = $this->patternA->patternAtShape(new TestShape(), TupleFactory::createPoint($x, $y, $z));
        Assert::assertTrue(ColorFactory::createWhite()->isEqualTo($actual));
    }

    /** @Then /^pattern_at\((pattern), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\) = (black)$/ */
    public function patternAtPatternPointIsBlack(string $expression, float $x, float $y, float $z): void
    {
        $actual = $this->patternA->patternAtShape(new TestShape(), TupleFactory::createPoint($x, $y, $z));
        Assert::assertTrue(ColorFactory::createBlack()->isEqualTo($actual));
    }

    /** @Given /^pattern_at\((pattern), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\) = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function patternAtPatternPointIsColor(
        string $expression,
        float $x,
        float $y,
        float $z,
        float $red,
        float $green,
        float $blue,
    ): void {
        $actual = $this->patternA->patternAtShape(new TestShape(), TupleFactory::createPoint($x, $y, $z));
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($actual));
    }

    /** @Given /^pattern is a ring_pattern\((white), (black)\)$/ */
    public function patternIsARingPatternWhiteBlack(): void
    {
        $this->patternA = PatternFactory::createRingPattern($this->colorContext->colorB, $this->colorContext->colorA);
    }

    /** @Given /^pattern is a checkers_pattern\((white), (black)\)$/ */
    public function patternIsACheckersPatternWhiteBlack(): void
    {
        $this->patternA = PatternFactory::createCheckerPattern($this->colorContext->colorB, $this->colorContext->colorA);
    }
}
