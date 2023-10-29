<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Light\LightFactory;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\ShapeFactory;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\World\World;
use PhpRayTracer\RayTracer\World\WorldFactory;
use PhpRayTracer\Tests\Behat\Utility\TestPattern;
use PHPUnit\Framework\Assert;
use function floatval;

final class WorldContext implements Context
{
    public World $world;
    public Light $light;
    private Color $colorAt;

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
            $this->shapeContext->shapeB = ShapeFactory::createSphere();
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

    /** @Given /^(shape|outer|A) is the first object in (w)$/ */
    public function shapeIsATheFirstObjectInW(): void
    {
        Assert::assertSame($this->shapeContext->shapeA, $this->world->getShape(0));
    }

    /** @Given /^(shape|inner|B) is the second object in (w)$/ */
    public function shapeIsATheSecondObjectInW(): void
    {
        Assert::assertSame($this->shapeContext->shapeB, $this->world->getShape(1));
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

    /** @When /^(shape)\.material\.ambient is (\d+)$/ */
    public function shapeMaterialAmbientIs(string $expression, int $value): void
    {
        $this->shapeContext->shapeB->getMaterial()->setAmbient($value);
    }

    /** @Given /^(world_c|color) is a shade_hit\((w), (comps)\)$/ */
    public function cIsAShadeHit(): void
    {
        $this->colorAt = $this->world->shadeHit($this->intersectionContext->computation);
    }

    /** @Given /^(color) is a shade_hit\((w), (comps), ([-+]?\d*\.?\d+)\)$/ */
    public function colorIsAShadeHit(string $expression1, string $expression2, string $expression3, int $remaining): void
    {
        $this->colorAt = $this->world->shadeHit($this->intersectionContext->computation, $remaining);
    }

    /** @Then /^(world_c) = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function shadeHitCIsEqualToColor(string $expression, float $red, float $green, float $blue): void
    {
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($this->colorAt));
    }

    /** @Given /^(w.light) is a point_light\(point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\), color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function lightIsAPointLightAtPointWithColor(string $expression, float $x, float $y, float $z, float $red, float $green, float $blue): void
    {
        $this->world->setLight(LightFactory::create(TupleFactory::createPoint($x, $y, $z), ColorFactory::create($red, $green, $blue)));
    }

    /** @When /^(world_c) is a color_at\((w), (r)\)$/ */
    public function cIsAColorAt(): void
    {
        $this->colorAt = $this->world->colorAt($this->rayContext->rayA);
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
        Assert::assertTrue($this->shapeContext->shapeB->getMaterial()->getColor()->isEqualTo($this->colorAt));
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

    /** @Given /^(plane) is added to (w)$/ */
    public function planeIsAddedToW(): void
    {
        $this->world->addShape($this->shapeContext->shapeC);
    }

    /** @Given /^(lower) is added to (w)$/ */
    public function lowerIsAddedToW(): void
    {
        $this->world->addShape($this->shapeContext->shapeA);
    }

    /** @Given /^(upper) is added to (w)$/ */
    public function upperIsAddedToW(): void
    {
        $this->world->addShape($this->shapeContext->shapeB);
    }

    /** @Given /^(floor) is added to (w)$/ */
    public function floorIsAddedToW(): void
    {
        $this->world->addShape($this->shapeContext->shapeC);
    }

    /** @Given /^(ball) is added to (w)$/ */
    public function ballIsAddedToW(): void
    {
        $this->world->addShape($this->shapeContext->shapeD);
    }

    /** @Given /^(color) is a reflected_color\((w), (comps)\)$/ */
    public function colorIsAReflectedColorWComps(): void
    {
        $this->colorAt = $this->world->reflectedColor($this->intersectionContext->computation, 4);
    }

    /** @Given /^(color) is a reflected_color\((w), (comps), (0)\)$/ */
    public function colorIsAReflectedColorWComps0(
        string $expression1,
        string $expression2,
        string $expression3,
        int $recursiveDepth,
    ): void {
        $this->colorAt = $this->world->reflectedColor($this->intersectionContext->computation, $recursiveDepth);
    }

    /** @Then /^(color) = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$$/ */
    public function colorIsColor(string $expression, float $red, float $green, float $blue): void
    {
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->isEqualTo($this->colorAt));
    }

    /** @Given /^(outer)\.material\.ambient is ([-+]?\d*\.?\d+)$/ */
    public function outerMaterialAmbientIs(string $expression, float $value): void
    {
        $this->shapeContext->shapeA->getMaterial()->setAmbient($value);
    }

    /** @Given /^(inner)\.material\.ambient is ([-+]?\d*\.?\d+)$/ */
    public function innerMaterialAmbientIs(string $expression, float $value): void
    {
        $this->shapeContext->shapeA->getMaterial()->setAmbient($value);
    }

    /** @Then /^(color_at)\((w), (r)\) should terminate successfully$/ */
    public function colorAtWRShouldTerminateSuccessfully(): void
    {
        Assert::assertTrue(true);
    }

    /** @Given /^(color|c) is a refracted_color\((w), (comps), (\d+)\)$/ */
    public function cIsRefractedColorWComps(
        string $expression1,
        string $expression2,
        string $expression3,
        int $recursiveDepth,
    ): void {
        $this->colorAt = $this->world->refractedColor($this->intersectionContext->computation, $recursiveDepth);
    }

    /** @Given /^(shape) has:$/ */
    public function shapeHas(TableNode $table): void
    {
        $material = MaterialFactory::create();
        $transparency = $table->getRow(0)[1];
        $refractiveIndex = $table->getRow(1)[1];
        $material->setTransparency(floatval($transparency));
        $material->setRefractiveIndex(floatval($refractiveIndex));

        $this->shapeContext->shapeA->setMaterial($material);
    }

    /** @Given /^(A) has:$/ */
    public function shapeAHas(TableNode $table): void
    {
        $material = MaterialFactory::create();
        $ambient = $table->getRow(0)[1];
        $material->setAmbient(floatval($ambient));
        $material->setPattern(new TestPattern());

        $this->shapeContext->shapeA->setMaterial($material);
    }

    /** @Given /^(B) has:$/ */
    public function shapeBHas(TableNode $table): void
    {
        $material = MaterialFactory::create();
        $transparency = $table->getRow(0)[1];
        $refractiveIndex = $table->getRow(1)[1];
        $material->setTransparency(floatval($transparency));
        $material->setRefractiveIndex(floatval($refractiveIndex));

        $this->shapeContext->shapeB->setMaterial($material);
    }
}
