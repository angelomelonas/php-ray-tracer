<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use LogicException;
use PhpRayTracer\RayTracer\Material\Material;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\ShapeFactory;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PHPUnit\Framework\Assert;
use function assert;
use function explode;
use function floatval;
use function is_int;
use function is_string;
use function strpos;
use function substr;
use function trim;

final class SphereContext implements Context
{
    private ShapeContext $shapeContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->shapeContext = $environment->getContext(ShapeContext::class);
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
        $material->setColor(ColorFactory::create(floatval($red), floatval($green), floatval($blue)));
        $material->setDiffuse(floatval($table->getRow(1)[1]));
        $material->setSpecular(floatval($table->getRow(2)[1]));

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

    /** @Given /^(s2|shape) is a glass_sphere\(\) with:$/ */
    public function s2IsAGlassSphereWith(string $expression, TableNode $table): void
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

    /** @Given /^(s|shape) is a glass_sphere\(\)$/ */
    public function sIsAGlassSphere(): void
    {
        $material = MaterialFactory::create();
        $material->setTransparency(1);
        $material->setRefractiveIndex(1.5);

        $this->createSphere($material);
    }

    /** @Given /^(A|B|C) is a glass_sphere\(\) with:$/ */
    public function AIsAGlassSphereWith(string $expression, TableNode $table): void
    {
        $row = $table->getRow(0)[1];
        assert(is_string($row));

        $length = strpos($row, '(');
        assert(is_int($length));
        $transformName = substr($row, 0, $length);
        $transform = null;

        if ($transformName === 'scaling') {
            $values = explode(', ', trim($row, 'scaling()'));
            [$x, $y, $z] = $values;

            $transform = MatrixFactory::createScaling(floatval($x), floatval($y), floatval($z));
        }

        if ($transformName === 'translation') {
            $values = explode(', ', trim($row, 'translation()'));
            [$x, $y, $z] = $values;

            $transform = MatrixFactory::createTranslation(floatval($x), floatval($y), floatval($z));
        }

        $refractiveIndex = floatval($table->getRow(1)[1]);
        $material = MaterialFactory::create();
        $material->setRefractiveIndex($refractiveIndex);

        $this->createSphere($material, $transform);
    }

    /** @Given /^(ball) is a sphere\(\) with:$/ */
    public function ballIsASphereWith(string $expression, TableNode $table): void
    {
        $material = MaterialFactory::create();

        $values = explode(', ', trim($table->getRow(0)[1], '()'));
        [$red, $green, $blue] = $values;
        $material->setColor(ColorFactory::create(floatval($red), floatval($green), floatval($blue)));
        $material->setAmbient(floatval($table->getRow(1)[1]));

        $values = explode(', ', trim($table->getRow(2)[1], '()'));
        [$x, $y, $z] = $values;
        $transform = MatrixFactory::createTranslation(floatval($x), floatval($y), floatval($z));

        $this->createSphere($material, $transform);
    }

    /** @Given /^(s)\.material\.transparency = ([-+]?\d*\.?\d+)$/ */
    public function sMaterialTransparency(string $expression, float $value): void
    {
        Assert::assertEquals($this->shapeContext->shapeA->getMaterial()->getTransparency(), $value);
    }

    /** @Given /^(s)\.material\.refractive_index = ([-+]?\d*\.?\d+)$/ */
    public function sMaterialRefractiveIndex(string $expression, float $value): void
    {
        Assert::assertEquals($this->shapeContext->shapeA->getMaterial()->getRefractiveIndex(), $value);
    }

    private function createSphere(?Material $material = null, ?Matrix $transform = null): void
    {
        $newSphere = ShapeFactory::createSphere();
        if ($material) {
            $newSphere->setMaterial($material);
        }

        if ($transform) {
            $newSphere->setTransform($transform);
        }

        if (! isset($this->shapeContext->shapeA)) {
            $this->shapeContext->shapeA = $newSphere;

            return;
        }

        if (! isset($this->shapeContext->shapeB)) {
            $this->shapeContext->shapeB = $newSphere;

            return;
        }

        if (! isset($this->shapeContext->shapeC)) {
            $this->shapeContext->shapeC = $newSphere;

            return;
        }

        if (! isset($this->shapeContext->shapeD)) {
            $this->shapeContext->shapeD = $newSphere;

            return;
        }

        throw new LogicException('No Sphere is set.');
    }
}
