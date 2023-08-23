<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\Sphere;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;
use function sqrt;
use const M_PI;

final class SphereContext implements Context
{
    public Sphere $sphere;

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

    /** @Given /^(s|shape) is a sphere\(\)$/ */
    public function sIsASphere(): void
    {
        $this->sphere = new Sphere();
    }

    /** @When /^(xs) is a intersect\((s), (r)\)$/ */
    public function intersectionIsAIntersectOfSphereAndRay(): void
    {
        $this->intersectionContext->intersections = new Intersections($this->sphere->intersect($this->rayContext->rayA));
    }

    /** @Given /^(xs)\[(\d+)\]\.object = (s)$/ */
    public function intersectionObjectAtIndexIsSphere(string $expression, int $index): void
    {
        Assert::assertSame($this->sphere, $this->intersectionContext->intersections->get($index)->getObject());
    }

    /** @Then /^(s)\.transform = identity_matrix$/ */
    public function sTransformIsIdentityMatrix(): void
    {
        $expected = MatrixFactory::createIdentity(MatrixFactory::MATRIX_4X4);
        $actual = $this->sphere->getTransform();
        Assert::assertTrue($expected->isEqualTo($actual));
    }

    /** @Given /^(t) is a translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function transformIsATranslation(string $expression, float $x, float $y, float $z): void
    {
        $this->transformationMatrix = MatrixFactory::createTranslation($x, $y, $z);
    }

    /** @When /^set_transform\((s), (t)\)$/ */
    public function setSphereTransform(): void
    {
        $this->sphere->setTransform($this->transformationMatrix);
    }

    /** @Then /^(s)\.transform = (t)$/ */
    public function sTransformIsT(): void
    {
        Assert::assertTrue($this->transformationMatrix->isEqualTo($this->sphere->getTransform()));
    }

    /** @When /^set_transform\(s, scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setTransformSScaling(float $x, float $y, float $z): void
    {
        $this->sphere->setTransform(MatrixFactory::createScaling($x, $y, $z));
    }

    /** @When /^set_transform\(s, translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setTransformSTranslation(float $x, float $y, float $z): void
    {
        $this->sphere->setTransform(MatrixFactory::createTranslation($x, $y, $z));
    }

    /** @When /^(n) is a normal_at\((s), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function nIsANormalAtPoint(string $expression, string $sphere, float $x, float $y, float $z): void
    {
        $this->normal = $this->sphere->normalAt(TupleFactory::createPoint($x, $y, $z));
    }

    /** @Then /^(n) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function nIsAVector(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($this->normal));
    }

    /** @When /^(n) is a normal_at\((s), point\((√3\/3), (√3\/3), (√3\/3)\)\)$/ */
    public function nIsANormalAtPoint3(): void
    {
        $this->normal = $this->sphere->normalAt(TupleFactory::createPoint(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3));
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
    public function setTransformOnSphere(): void
    {
        $this->sphere->setTransform($this->transformationMatrix);
    }

    /** @When /^(n) is a normal_at\((s), point\(0, √2\/2, \-√2\/2\)\)$/ */
    public function nIsANormalAtPoint2(): void
    {
        $this->normal = $this->sphere->normalAt(TupleFactory::createPoint(0, sqrt(2) / 2, -sqrt(2) / 2));
    }

    /** @When /^(m) is a (s)\.material$/ */
    public function mIsASMaterial(): void
    {
        $this->materialContext->material = $this->sphere->getMaterial();
    }

    /** @Then /^(m) = material\(\)$/ */
    public function mMaterialIsTheDefaultMaterial(): void
    {
        Assert::assertEquals(MaterialFactory::create(), $this->materialContext->material);
    }

    /** @When /^(m)\.ambient is a (\d+)$/ */
    public function mMaterialAmbientIs(string $expression, int $value): void
    {
        $this->materialContext->material->ambient = $value;
    }

    /** @When /^s\.material is a (m)$/ */
    public function sMaterialIsAM(): void
    {
        $this->sphere->setMaterial($this->materialContext->material);
    }

    /** @Then /^s\.material = (m)$/ */
    public function sMaterialM(): void
    {
        Assert::assertEquals($this->materialContext->material, $this->sphere->getMaterial());
    }

//    /**
//     * @Given /^s is a glass_sphere\(\)$/
//     */
//    public function sIsAGlass_sphere()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^s\.material\.transparency = (\d+)\.(\d+)$/
//     */
//    public function sMaterialTransparency($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^s\.material\.refractive_index = (\d+)\.(\d+)$/
//     */
//    public function sMaterialRefractive_index($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
}
