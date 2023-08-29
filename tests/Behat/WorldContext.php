<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Light\LightFactory;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Shape\ShapeFactory;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\World\World;
use PhpRayTracer\RayTracer\World\WorldFactory;
use PHPUnit\Framework\Assert;

final class WorldContext implements Context
{
    public World $world;
    public Light $light;
    private Color $shadeHit;

    private LightContext $lightContext;
    private RayContext $rayContext;
    private IntersectionContext $intersectionContext;
    private TupleContext $tupleContext;
    private ShapeContext $shapeContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->lightContext = $environment->getContext(LightContext::class);
        /* @phpstan-ignore-next-line */
        $this->rayContext = $environment->getContext(RayContext::class);
        /* @phpstan-ignore-next-line */
        $this->intersectionContext = $environment->getContext(IntersectionContext::class);
        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
        /* @phpstan-ignore-next-line */
        $this->shapeContext = $environment->getContext(ShapeContext::class);
    }

    /** @Given /^(w) is a world\(\)$/ */
    public function wIsAWorld(): void
    {
        $this->world = WorldFactory::create();
    }

    /** @Then /^(w) contains no objects$/ */
    public function worldContainsNoObjects(): void
    {
        Assert::assertTrue($this->world->isEmpty());
    }

    /** @Given /^(w) has no light source$/ */
    public function worldHasNoLightSource(): void
    {
        Assert::assertNull($this->world->getLight());
    }

    /** @When /^(w) is a default_world\(\)$/ */
    public function wIsADefaultWorld(): void
    {
        $this->world = WorldFactory::create();

        if (! isset($this->lightContext->light)) {
            $this->lightContext->light = LightFactory::create(TupleFactory::createPoint(-10.0, 10.0, -10.0), ColorFactory::createWhite());
        }

        $this->world->setLight($this->lightContext->light);

        if (! isset($this->shapeContext->shapeA)) {
            $this->shapeContext->shapeA = ShapeFactory::createSphere();
            $material = MaterialFactory::create();
            $material->setColor(ColorFactory::create(0.8, 1.0, 0.6));
            $material->setDiffuse(0.7);
            $material->setSpecular(0.2);
            $this->shapeContext->shapeA->setMaterial($material);
        }

        $this->world->addShape($this->shapeContext->shapeA);

        if (! isset($this->shapeContext->shapeB)) {
            $this->shapeContext->shapeB= ShapeFactory::createSphere();
            $this->shapeContext->shapeB->setTransform(MatrixFactory::createScaling(0.5, 0.5, 0.5));
        }

        $this->world->addShape($this->shapeContext->shapeB);
    }

    /** @Then /^(w)\.light = (light)$/ */
    public function wLightIsLight(): void
    {
        Assert::assertNotNull($this->world->getLight());
        Assert::assertTrue($this->lightContext->light->isEqualTo($this->world->getLight()));
    }

    /** @Given /^(w) contains (s1)$/ */
    public function wContainsSphereS1(): void
    {
        Assert::assertSame($this->shapeContext->shapeA, $this->world->getShape(0));
    }

    /** @Given /^(w) contains (s2)$/ */
    public function wContainsSphereS2(): void
    {
        Assert::assertSame($this->shapeContext->shapeB, $this->world->getShape(1));
    }

    /** @When /^(xs) is the intersections of intersect_world\((w), (r)\)$/ */
    public function xsIsTheIntersectionsOfIntersectWorld(): void
    {
        $this->intersectionContext->intersections = $this->world->intersectWorld($this->rayContext->rayA);
    }

    /** @Given /^(shape|outer) is the first object in (w)$/ */
    public function shapeIsATheFirstObjectInW(): void
    {
        Assert::assertSame($this->shapeContext->shapeA, $this->world->getShape(0));
    }

    /** @When /^([^"]+) is a intersection\(([-+]?\d*\.?\d+), (shapeA)\)$/ */
    public function iIsAIntersectionOfWorldShapeA(string $expression, float $t): void
    {
        $this->intersectionContext->createIntersection($t, $this->shapeContext->shapeA);
    }

    /** @When /^([^"]+) is a intersection\(([-+]?\d*\.?\d+), (shapeB)\)$/ */
    public function iIsAIntersectionOfWorldShapeB(string $expression, float $t): void
    {
        $this->intersectionContext->createIntersection($t, $this->shapeContext->shapeB);
    }

    /** @Given /^(shape|inner) is the second object in (w)$/ */
    public function shapeIsATheSecondObjectInW(): void
    {
        Assert::assertSame($this->shapeContext->shapeB, $this->world->getShape(1));
    }

    /** @Given /^(world_c) is a shade_hit\((w), (comps)\)$/ */
    public function cIsAShadeHit(): void
    {
        $this->shadeHit = $this->world->shadeHit($this->intersectionContext->computation);
    }

    /** @Then /^(world_c) = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function shadeHitCIsEqualToColor(string $expression, float $red, float $green, float $blue): void
    {
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($this->shadeHit));
    }

    /** @Given /^(w.light) is a point_light\(point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\), color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function lightIsAPointLightAtPointWithColor(string $expression, float $x, float $y, float $z, float $red, float $green, float $blue): void
    {
        $this->world->setLight(LightFactory::create(TupleFactory::createPoint($x, $y, $z), ColorFactory::create($red, $green, $blue)));
    }

    /** @When /^(world_c) is a color_at\((w), (r)\)$/ */
    public function cIsAColorAt(): void
    {
        $this->shadeHit = $this->world->colorAt($this->rayContext->rayA);
    }

    /** @Given /^(outer)\.(material)\.(ambient) is a ([-+]?\d*\.?\d+)$/ */
    public function shapeAMaterialAmbientIsA(string $expression1, string $expression2, string $expression3, float $value): void
    {
        $this->shapeContext->shapeA->getMaterial()->setAmbient($value);
    }

    /** @Given /^(inner)\.(material)\.(ambient) is a ([-+]?\d*\.?\d+)$/ */
    public function shapeBMaterialAmbientIsA(string $expression1, string $expression2, string $expression3, float $value): void
    {
        $this->shapeContext->shapeB->getMaterial()->setAmbient($value);
    }

    /** @Then /^(world_c) = (inner)\.material\.color$/ */
    public function cShapeMaterialColor(): void
    {
        Assert::assertTrue($this->shapeContext->shapeB->getMaterial()->getColor()->isEqualTo($this->shadeHit));
    }

    /** @Then /^is_shadowed\((w), (p)\) is (false|true)$/ */
    public function isShadowedIsFalse(string $expression1, string $expression2, string $value): void
    {
        Assert::assertTrue($this->world->isShadowed($this->tupleContext->tupleA) === ($value === 'true'));
    }

    /** @Given /^(s1) is added to (w)$/ */
    public function sphereS1IsAddedToW(): void
    {
        $this->world->addShape($this->shapeContext->shapeA);
    }

    /** @Given /^(s2) is added to (w)$/ */
    public function sphereS2IsAddedToW(): void
    {
        $this->world->addShape($this->shapeContext->shapeB);
    }
}
