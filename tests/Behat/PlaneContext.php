<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use LogicException;
use PhpRayTracer\RayTracer\Intersection\Intersection;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\ShapeFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PHPUnit\Framework\Assert;
use function count;
use function explode;
use function floatval;
use function trim;

final class PlaneContext implements Context
{
    /** @var Intersection[] */
    private array $localIntersections;

    private ShapeContext $shapeContext;
    private RayContext $rayContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->shapeContext = $environment->getContext(ShapeContext::class);
        /* @phpstan-ignore-next-line */
        $this->rayContext = $environment->getContext(RayContext::class);
    }

    /** @Given /^(p|shape) is a plane\(\)$/ */
    public function pIsAPlane(): void
    {
        $this->createPlane();
    }

    /** @Given /^(plane|lower|upper) is a plane\(\) with:$/ */
    public function shapeIsASPlaneWith(string $expression, TableNode $table): void
    {
        $material = MaterialFactory::create();
        $reflectivity = $table->getRow(0)[1];
        $material->setReflective(floatval($reflectivity));

        $values = explode(', ', trim($table->getRow(1)[1], '()'));
        [$x, $y, $z] = $values;
        $transform = MatrixFactory::createTranslation(floatval($x), floatval($y), floatval($z));

        $this->createPlane($material, $transform);
    }

    private function createPlane(?Material $material = null, ?Matrix $transform = null): void
    {
        $newPlane = ShapeFactory::createPlane();
        if ($material) {
            $newPlane->setMaterial($material);
        }

        if ($transform) {
            $newPlane->setTransform($transform);
        }

        if (! isset($this->shapeContext->shapeA)) {
            $this->shapeContext->shapeA = $newPlane;

            return;
        }

        if (! isset($this->shapeContext->shapeB)) {
            $this->shapeContext->shapeB = $newPlane;

            return;
        }

        if (! isset($this->shapeContext->shapeC)) {
            $this->shapeContext->shapeC = $newPlane;

            return;
        }

        throw new LogicException('No Plane is set.');
    }

    /** @When /^(n1|n2|n3) is a local_normal_at\((p), point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/ */
    public function nIsALocalNormalAtPoint(string $expression1, string $expression2, float $x, float $y, float $z): void
    {
    }

    /** @Given /^(n1|n2|n3) = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function nIsAVector(string $expression, float $x, float $y, float $z): void
    {
        $actual = $this->shapeContext->shapeA->normalAt(TupleFactory::createPoint($x, $y, $z));
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($actual));
    }

    /** @When /^(lxs) is a local_intersect\((p), (r)\)$/ */
    public function xsIsALocalIntersectPR(): void
    {
        $this->localIntersections = $this->shapeContext->shapeA->intersect($this->rayContext->rayA);
    }

    /** @Then /^(lxs) is empty$/ */
    public function xsIsEmpty(): void
    {
        Assert::assertEmpty($this->localIntersections);
    }

    /** @Then /^(lxs)\.count = (\d+)$/ */
    public function intersectionCount(string $expression, int $count): void
    {
        Assert::assertCount($count, $this->localIntersections);
    }

    /** @Given /^(lxs)\[(\d+)\]\.t = ([-+]?\d*\.?\d+)$/ */
    public function intersectionObjectAtIndexIsT(string $expression, int $index, float $value): void
    {
        Assert::assertSame($value, $this->localIntersections[$index]->getT());
    }

    /** @Given /^(lxs)\[(\d+)\]\.object = (p)$/ */
    public function intersectionObjectAtIndexIsObject(string $expression, int $index): void
    {
        Assert::assertSame($this->shapeContext->shapeA, $this->localIntersections[$index]->getShape());
    }

    /** @Given /^(floor) is a plane\(\) with:$/ */
    public function floorIsAPlaneWith(string $expression, TableNode $table): void
    {
        $material = MaterialFactory::create();

        $values = explode(', ', trim($table->getRow(0)[1], '()'));
        [$x, $y, $z] = $values;
        $transform = MatrixFactory::createTranslation(floatval($x), floatval($y), floatval($z));

        for ($i = 1; $i < count($table->getLines()); $i++) {
            $row = $table->getRow($i);

            if ($row[0] === 'material.reflective') {
                $material->setReflective(floatval($row[1]));
            }

            if ($row[0] === 'material.transparency') {
                $material->setTransparency(floatval($row[1]));
            }

            if ($row[0] === 'material.refractive_index') {
                $material->setRefractiveIndex(floatval($row[1]));
            }
        }

        $this->createPlane($material, $transform);
    }
}
