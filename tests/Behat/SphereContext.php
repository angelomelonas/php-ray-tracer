<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use LogicException;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\ShapeFactory;
use PhpRayTracer\RayTracer\Shape\Sphere;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;
use function assert;
use function explode;
use function floatval;
use function is_int;
use function is_string;
use function sqrt;
use function strpos;
use function substr;
use function trim;
use const M_PI;

final class SphereContext implements Context
{
    public Sphere $sphereA;
    public Sphere $sphereB;

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

    /** @Given /^(s|s1|shape) is a sphere\(\)$/ */
    public function sIsASphere(): void
    {
        $this->createSphere();
    }

    /** @Given /^(s1) is a sphere\(\) with:$/ */
    public function s1IsASphereWith(string $expression, TableNode $table): void
    {
        $values = explode(', ', trim($table->getRow(0)[1], '()'));
        [$red, $green, $blue] = $values;

        $material = MaterialFactory::create();
        $material->color = ColorFactory::create(floatval($red), floatval($green), floatval($blue));
        $material->diffuse = floatval($table->getRow(1)[1]);
        $material->specular = floatval($table->getRow(2)[1]);

        $this->createSphere($material);
    }

    /** @Given /^(s2|shape) is a sphere\(\) with:$/ */
    public function s2IsASphereWith(string $expression, TableNode $table): void
    {
        $row = $table->getRow(0)[1];
        assert(is_string($row));

        $values = explode(', ', trim($row, '()'));
        [$x, $y, $z] = $values;

        $length = strpos($row, '(');
        assert(is_int($length));
        $transformName = substr($row, 0, $length);
        $transform = null;
        if ($transformName === 'scaling') {
            $transform = MatrixFactory::createScaling(floatval($x), floatval($y), floatval($z));
        }

        if ($transformName === 'translation') {
            $transform = MatrixFactory::createTranslation(floatval($x), floatval($y), floatval($z));
        }

        $this->createSphere(null, $transform);
    }

    /** @When /^(xs) is a intersect\((s), (r)\)$/ */
    public function intersectionIsAIntersectOfSphereAndRay(): void
    {
        $this->intersectionContext->intersections = new Intersections($this->sphereA->intersect($this->rayContext->rayA));
    }

    /** @Given /^(xs)\[(\d+)\]\.object = (s)$/ */
    public function intersectionObjectAtIndexIsSphere(string $expression, int $index): void
    {
        Assert::assertSame($this->sphereA, $this->intersectionContext->intersections->get($index)->getObject());
    }

    /** @Then /^(s)\.transform = identity_matrix$/ */
    public function sTransformIsIdentityMatrix(): void
    {
        $expected = MatrixFactory::createIdentity(MatrixFactory::MATRIX_4X4);
        $actual = $this->sphereA->getTransform();
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
        $this->sphereA->setTransform($this->transformationMatrix);
    }

    /** @Then /^(s)\.transform = (t)$/ */
    public function sTransformIsT(): void
    {
        Assert::assertTrue($this->transformationMatrix->isEqualTo($this->sphereA->getTransform()));
    }

    /** @When /^set_transform\(s, scaling\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setTransformSScaling(float $x, float $y, float $z): void
    {
        $this->sphereA->setTransform(MatrixFactory::createScaling($x, $y, $z));
    }

    /** @When /^set_transform\(s, translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function setTransformSTranslation(float $x, float $y, float $z): void
    {
        $this->sphereA->setTransform(MatrixFactory::createTranslation($x, $y, $z));
    }

    /** @When /^(n) is a normal_at\((s), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function nIsANormalAtPoint(string $expression, string $sphere, float $x, float $y, float $z): void
    {
        $this->normal = $this->sphereA->normalAt(TupleFactory::createPoint($x, $y, $z));
    }

    /** @Then /^(n) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function nIsAVector(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($this->normal));
    }

    /** @When /^(n) is a normal_at\((s), point\((√3\/3), (√3\/3), (√3\/3)\)\)$/ */
    public function nIsANormalAtPoint3(): void
    {
        $this->normal = $this->sphereA->normalAt(TupleFactory::createPoint(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3));
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
        $this->sphereA->setTransform($this->transformationMatrix);
    }

    /** @When /^(n) is a normal_at\((s), point\(0, √2\/2, \-√2\/2\)\)$/ */
    public function nIsANormalAtPoint2(): void
    {
        $this->normal = $this->sphereA->normalAt(TupleFactory::createPoint(0, sqrt(2) / 2, -sqrt(2) / 2));
    }

    /** @When /^(m) is a (s)\.material$/ */
    public function mIsASMaterial(): void
    {
        $this->materialContext->material = $this->sphereA->getMaterial();
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
        $this->sphereA->setMaterial($this->materialContext->material);
    }

    /** @Then /^s\.material = (m)$/ */
    public function sMaterialM(): void
    {
        Assert::assertEquals($this->materialContext->material, $this->sphereA->getMaterial());
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

    private function createSphere(?Material $material = null, ?Matrix $transform = null): void
    {
        $newSphere = ShapeFactory::createSphere();
        if ($material) {
            $newSphere->setMaterial($material);
        }

        if ($transform) {
            $newSphere->setTransform($transform);
        }

        if (! isset($this->sphereA)) {
            $this->sphereA = $newSphere;

            return;
        }

        if (! isset($this->sphereB)) {
            $this->sphereB = $newSphere;

            return;
        }

        throw new LogicException('No Sphere is set.');
    }
}
