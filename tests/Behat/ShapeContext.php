<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\Tests\Behat\Utility\TestShape;
use PHPUnit\Framework\Assert;
use function assert;
use function get_parent_class;
use function sqrt;
use const M_PI;

final class ShapeContext implements Context
{
    public Shape $shapeA;
    public Shape $shapeB;
    public Shape $shapeC;
    public Shape $shapeD;
    public Tuple $normal;
    public Matrix $transformationMatrix;

    private RayContext $rayContext;
    private MaterialContext $materialContext;
    private IntersectionContext $intersectionContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->rayContext = $environment->getContext(RayContext::class);
        /* @phpstan-ignore-next-line */
        $this->materialContext = $environment->getContext(MaterialContext::class);
        /* @phpstan-ignore-next-line */
        $this->intersectionContext = $environment->getContext(IntersectionContext::class);
    }

    /** @Given /^(s) is a test_shape\(\)$/ */
    public function sIsATestShape(): void
    {
        $this->shapeA = new TestShape();
    }

    /** @When /^(xs) is a intersect\((s), (r)\)$/ */
    public function intersectionIsAIntersectOfShapeAndRay(): void
    {
        $this->intersectionContext->intersections = new Intersections($this->shapeA->intersect($this->rayContext->rayA));
    }

    /** @Given /^(xs)\[(\d+)\]\.object = (s)$/ */
    public function intersectionObjectAtIndexIsShape(string $expression, int $index): void
    {
        Assert::assertSame($this->shapeA, $this->intersectionContext->intersections->get($index)->getShape());
    }

    /** @Then /^(s)\.transform = identity_matrix$/ */
    public function sTransformIsIdentityMatrix(): void
    {
        $expected = MatrixFactory::createIdentity();
        $actual = $this->shapeA->getTransform();
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /** @Given /^(t) is a translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformIsATranslation(string $expression, float $x, float $y, float $z): void
    {
        $this->transformationMatrix = MatrixFactory::createTranslation($x, $y, $z);
    }

    /** @When /^set_transform\((s), (t)\)$/ */
    public function setShapeTransform(): void
    {
        $this->shapeA->setTransform($this->transformationMatrix);
    }

    /** @Then /^(s)\.transform = (t)$/ */
    public function sTransformIsT(): void
    {
        Assert::assertTrue($this->transformationMatrix->isEqualTo($this->shapeA->getTransform()));
    }

    /** @When /^set_transform\(s, scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setTransformSScaling(float $x, float $y, float $z): void
    {
        $this->shapeA->setTransform(MatrixFactory::createScaling($x, $y, $z));
    }

    /** @When /^set_transform\(s, translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setTransformSTranslation(float $x, float $y, float $z): void
    {
        $this->shapeA->setTransform(MatrixFactory::createTranslation($x, $y, $z));
    }

    /** @When /^(n) is a normal_at\((s), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function nIsANormalAtPoint(string $expression, string $shape, float $x, float $y, float $z): void
    {
        $this->normal = $this->shapeA->normalAt(TupleFactory::createPoint($x, $y, $z));
    }

    /** @Then /^(n) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function nIsAVector(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($this->normal));
    }

    /** @When /^(n) is a normal_at\((s), point\((√3\/3), (√3\/3), (√3\/3)\)\)$/ */
    public function nIsANormalAtPoint3(): void
    {
        $this->normal = $this->shapeA->normalAt(TupleFactory::createPoint(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3));
    }

    /** @Then /^(n) = vector\((√3\/3), (√3\/3), (√3\/3)\)$/ */
    public function nIsAVector3(): void
    {
        Assert::assertTrue(TupleFactory::createVector(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3)->isEqualTo($this->normal));
    }

    /** @Then /^(n) = normalize\((n)\)$/ */
    public function nNormalizeN(): void
    {
        Assert::assertTrue($this->normal->normalize()->isEqualTo($this->normal));
    }

    /** @Given /^(m) is a scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\) \* rotation_z\((π\/5)\)$/ */
    public function mIsAScalingAndARotation(string $expression, float $x, float $y, float $z): void
    {
        $this->transformationMatrix = MatrixFactory::createScaling($x, $y, $z)->multiplyMatrix(MatrixFactory::createRotationZ(M_PI / 5));
    }

    /** @Given /^set_transform\((s), (m)\)$/ */
    public function setTransformOnShape(): void
    {
        $this->shapeA->setTransform($this->transformationMatrix);
    }

    /** @When /^(n) is a normal_at\((s), point\(0, √2\/2, \-√2\/2\)\)$/ */
    public function nIsANormalAtPoint2(): void
    {
        $this->normal = $this->shapeA->normalAt(TupleFactory::createPoint(0, sqrt(2) / 2, -sqrt(2) / 2));
    }

    /** @When /^(m) is a (s)\.material$/ */
    public function mIsASMaterial(): void
    {
        $this->materialContext->material = $this->shapeA->getMaterial();
    }

    /** @Then /^(m) = material\(\)$/ */
    public function mMaterialIsTheDefaultMaterial(): void
    {
        Assert::assertEquals(MaterialFactory::create(), $this->materialContext->material);
    }

    /** @When /^(m)\.ambient is (\d+)$/ */
    public function mMaterialAmbientIs(string $expression, int $value): void
    {
        $this->materialContext->material->setAmbient($value);
    }

    /** @When /^s\.material is a (m)$/ */
    public function sMaterialIsAM(): void
    {
        $this->shapeA->setMaterial($this->materialContext->material);
    }

    /** @Then /^s\.material = (m)$/ */
    public function sMaterialM(): void
    {
        Assert::assertEquals($this->materialContext->material, $this->shapeA->getMaterial());
    }

    /** @Then /^(s)\.transform = translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function sTransformTranslation4(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(MatrixFactory::createTranslation($x, $y, $z)->isEqualTo($this->shapeA->getTransform()));
    }

    /** @Given /^(s)\.saved_ray\.direction = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function sSavedRayDirectionVector(string $expression, float $x, float $y, float $z): void
    {
        assert($this->shapeA instanceof TestShape);
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($this->shapeA->getSavedRay()->direction));
    }

    /** @Then /^(s)\.saved_ray\.origin = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function sSavedRayOriginPoint(string $expression, float $x, float $y, float $z): void
    {
        assert($this->shapeA instanceof TestShape);
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($this->shapeA->getSavedRay()->origin));
    }

    /** @Then /^(s)\.parent is nothing$/ */
    public function sParentIsNothing(): void
    {
        Assert::assertTrue(get_parent_class($this->shapeA) === Shape::class);
    }
}
