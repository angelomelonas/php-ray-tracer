<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use LogicException;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Shape\ShapeFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;

final class CubeContext implements Context
{
    private ShapeContext $shapeContext;
    private TupleContext $tupleContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->shapeContext = $environment->getContext(ShapeContext::class);
        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
    }

    /** @Given /^(c) is a cube\(\)$/ */
    public function cIsACube(): void
    {
        $this->createCube();
    }

    /** @When /^(normal) is a local_normal_at\((c),( p)\)$/ */
    public function normalIsALocalNormalAtCP(): void
    {
    }

    /** @Then /^(normal) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function normalIs(string $expression, float $x, float $y, float $z): void
    {
        $actual = $this->shapeContext->shapeA->normalAt($this->tupleContext->tupleA);
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($actual));
    }

    private function createCube(?Material $material = null, ?Matrix $transform = null): void
    {
        $newCube = ShapeFactory::createCube();
        if ($material) {
            $newCube->setMaterial($material);
        }

        if ($transform) {
            $newCube->setTransform($transform);
        }

        if (! isset($this->shapeContext->shapeA)) {
            $this->shapeContext->shapeA = $newCube;

            return;
        }

        throw new LogicException('No Cube is set.');
    }
}
