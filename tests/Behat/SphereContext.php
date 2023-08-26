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

        if (! isset($this->shapeContext->shapeA)) {
            $this->shapeContext->shapeA = $newSphere;

            return;
        }

        if (! isset($this->shapeContext->shapeB)) {
            $this->shapeContext->shapeB = $newSphere;

            return;
        }

        throw new LogicException('No Sphere is set.');
    }
}
