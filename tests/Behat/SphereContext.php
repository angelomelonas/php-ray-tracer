<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Intersection\Intersections;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Sphere\Sphere;
use PHPUnit\Framework\Assert;

final class SphereContext implements Context
{
    public Sphere $sphere;
    public Intersections $intersections;

    public Matrix $translationMatrix;

    private RayContext $rayContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->rayContext = $environment->getContext(RayContext::class);
    }

    /** @Given /^(s) is a sphere\(\)$/ */
    public function sIsASphere(): void
    {
        $this->sphere = new Sphere();
    }

    /** @When /^(xs) is a intersect\((s), (r)\)$/ */
    public function intersectionIsAIntersectOfSphereAndRay(): void
    {
        $this->intersections = $this->sphere->intersect($this->rayContext->rayA);
    }

    /** @Then /^(xs)\.count = (\d+)$/ */
    public function intersectionCount(string $expression, int $count): void
    {
        Assert::assertCount($count, $this->intersections);
    }

    /** @Given /^(xs)\[(\d+)\] = ([-+]?\d*\.?\d+)$/ */
    public function intersectionAtIndexIs(string $expression, int $index, float $value): void
    {
        Assert::assertSame($value, $this->intersections->get($index)->getT());
    }

    /** @Given /^(xs)\[(\d+)\]\.object = (s)$/ */
    public function intersectionObjectAtIndexIsSphere(string $expression, int $index): void
    {
        Assert::assertSame($this->sphere, $this->intersections->get($index)->getObject());
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
        $this->translationMatrix = MatrixFactory::createTranslation($x, $y, $z);
    }

    /** @When /^set_transform\((s), (t)\)$/ */
    public function setSphereTransform(): void
    {
        $this->sphere->setTransform($this->translationMatrix);
    }

    /** @Then /^(s)\.transform = (t)$/ */
    public function sTransformIsT(): void
    {
        Assert::assertTrue($this->translationMatrix->isEqualTo($this->sphere->getTransform()));
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

    /** @Given /^(xs)\[(\d+)\]\.t = ([-+]?\d*\.?\d+)$/ */
    public function intersectionObjectAtIndexIsT(string $expression, int $index, float $value): void
    {
        Assert::assertSame($value, $this->intersections->get($index)->getT());
    }

//    /**
//     * @When /^set_transform\(s, translation\(5, (\d+), 0\)\)$/
//     */
//    public function set_transformSTranslation0($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @When /^n is a normal_at\(s, point\(1, (\d+), 0\)\)$/
//     */
//    public function nIsANormal_atSPoint0($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^n = vector\(1, (\d+), 0\)$/
//     */
//    public function nVector0($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @When /^n is a normal_at\(s, point\(0, (\d+), 1\)\)$/
//     */
//    public function nIsANormal_atSPoint1($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^n = vector\(0, (\d+), 1\)$/
//     */
//    public function nVector1($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @When /^n is a normal_at\(s, point\(√3\/3, √3\/3, √3\/3\)\)$/
//     */
//    public function nIsANormal_atSPoint3333($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^n = vector\(√3\/3, √3\/3, √3\/3\)$/
//     */
//    public function nVector3333($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^n = normalize\(n\)$/
//     */
//    public function nNormalizeN()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @When /^n is a normal_at\(s, point\(0, (\d+)\.(\d+), \-0\.70711\)\)$/
//     */
//    public function nIsANormal_atSPoint70711($arg1, $arg2, $arg3, $arg4)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^n = vector\(0, (\d+)\.(\d+), \-0\.70711\)$/
//     */
//    public function nVector70711($arg1, $arg2, $arg3, $arg4)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^m is a scaling\(1, (\d+)\.(\d+), 1\) \* rotation_z\(π\/5\)$/
//     */
//    public function mIsAScaling1Rotation_zΠ5($arg1, $arg2, $arg3)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^set_transform\(s, m\)$/
//     */
//    public function set_transformSM()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @When /^n is a normal_at\(s, point\(0, √2\/2, \-√2\/2\)\)$/
//     */
//    public function nIsANormal_atSPoint222($arg1, $arg2)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^n = vector\(0, (\d+)\.(\d+), \-0\.24254\)$/
//     */
//    public function nVector24254($arg1, $arg2, $arg3, $arg4)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @When /^m is a s\.material$/
//     */
//    public function mIsASMaterial()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^m = material\(\)$/
//     */
//    public function mMaterial()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^m is a material\(\)$/
//     */
//    public function mIsAMaterial()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Given /^m\.ambient is a (\d+)$/
//     */
//    public function mAmbientIsA($arg1)
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @When /^s\.material is a m$/
//     */
//    public function sMaterialIsAM()
//    {
//        throw new PendingException();
//    }
//
//    /**
//     * @Then /^s\.material = m$/
//     */
//    public function sMaterialM()
//    {
//        throw new PendingException();
//    }
//
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
