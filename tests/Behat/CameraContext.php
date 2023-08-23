<?php
declare(strict_types=1);

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PhpRayTracer\RayTracer\Camera\Camera;
use PhpRayTracer\RayTracer\Camera\CameraFactory;
use PhpRayTracer\RayTracer\Canvas\Canvas;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\Utility\Utility;
use PHPUnit\Framework\Assert;
use function sqrt;
use const M_PI_2;
use const M_PI_4;

final class CameraContext implements Context
{
    private int $hSize;
    private int $vSize;
    private float $fieldOfView;
    private Camera $camera;
    private Ray $ray;
    private Canvas $canvasImage;

    private TupleContext $tupleContext;
    private WorldContext $worldContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        /* @phpstan-ignore-next-line */
        $this->tupleContext = $environment->getContext(TupleContext::class);
        /* @phpstan-ignore-next-line */
        $this->worldContext = $environment->getContext(WorldContext::class);
    }

    /** @Given /^hsize is (\d+)$/ */
    public function hSizeIs(int $size): void
    {
        $this->hSize = $size;
    }

    /** @Given /^vsize is (\d+)$/ */
    public function vSizeIs(int $size): void
    {
        $this->vSize = $size;
    }

    /** @Given /^field_of_view is (π\/2)$/ */
    public function fieldOfViewIsPi2(): void
    {
        $this->fieldOfView = M_PI_2;
    }

    /** @When /^(c) is a camera\(hsize, vsize, field_of_view\)$/ */
    public function cIsACameraHSizeByVSizeWithFieldOfView(): void
    {
        $this->camera = CameraFactory::create($this->hSize, $this->vSize, $this->fieldOfView);
    }

    /** @Then /^(c)\.hsize = (\d+)$/ */
    public function cameraHSize(string $expression, int $size): void
    {
        Assert::assertSame($size, $this->camera->getHSize());
    }

    /** @Then /^(c)\.vsize = (\d+)$/ */
    public function cameraVSize(string $expression, int $size): void
    {
        Assert::assertSame($size, $this->camera->getVSize());
    }

    /** @Given /^(c)\.field_of_view = (π\/2)$/ */
    public function cameraFieldOfView(): void
    {
        Assert::assertSame(M_PI_2, $this->camera->getFieldOfView());
    }

    /** @Given /^(c)\.transform = identity_matrix$/ */
    public function cameraTransformIsIdentityMatrix(string $expression): void
    {
        Assert::assertTrue(MatrixFactory::createIdentity()->isEqualTo($this->camera->getTransform()));
    }

    /** @Given /^(c) is a camera\((\d+), (\d+), (π\/2)\)$/ */
    public function cIsACamera(string $expression, int $hSize, int $vSize): void
    {
        $this->camera = CameraFactory::create($hSize, $vSize, M_PI_2);
    }

    /** @Then /^(c)\.pixel_size = ([-+]?\d*\.?\d+)$/ */
    public function cPixelSize(string $expression, float $value): void
    {
        Assert::assertTrue(Utility::areFloatsEqual($value, $this->camera->getPixelSize()));
    }

    /** @When /^(r) is a ray_for_pixel\((c), (\d+), (\d+)\)$/ */
    public function rIsARayForPixel(string $expression, string $camera, int $x, int $y): void
    {
        $this->ray = $this->camera->rayForPixel($x, $y);
    }

    /** @Then /^(r)\.origin = point\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function rOriginIsPoint(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createPoint($x, $y, $z)->isEqualTo($this->ray->origin));
    }

    /** @Given /^(r)\.direction = vector\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function rDirectionIsVector(string $expression, float $x, float $y, float $z): void
    {
        Assert::assertTrue(TupleFactory::createVector($x, $y, $z)->isEqualTo($this->ray->direction));
    }

    /** @When /^(c)\.transform is a rotation_y\((π\/4)\) \* translation\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function cTransformIsARotationAndATranslation(string $expression, string $rotationAngle, float $x, float $y, float $z): void
    {
        $this->camera->setTransform(MatrixFactory::createRotationY(M_PI_4)->multiplyMatrix(MatrixFactory::createTranslation($x, $y, $z)));
    }

    /** @Given /^(r)\.direction = vector\((√2\/2), (0), (\-√2\/2)\)$/ */
    public function rDirectionVectorIsVector222(): void
    {
        Assert::assertTrue(TupleFactory::createVector(sqrt(2) / 2, 0, -sqrt(2) / 2)->isEqualTo($this->ray->direction));
    }

    /** @Given /^(c)\.transform is a view_transform\((from), (to), (up)\)$/ */
    public function cTransformIsAViewTransformFromToUp(): void
    {
        $this->camera->setTransform(MatrixFactory::createViewTransformation(
            $this->tupleContext->tupleA,
            $this->tupleContext->tupleB,
            $this->tupleContext->tupleC
        ));
    }

    /** @When /^(image) is a render\((c), (w)\)$/ */
    public function imageIsARenderOfWorld(): void
    {
        $this->canvasImage = $this->camera->render($this->worldContext->world);
    }

    /** @Then /^pixel_at\((image), (\d+), (\d+)\) = color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/ */
    public function pixelAtIsColor(string $image, int $x, int $y, float $red, float $green, float $blue): void
    {
        Assert::assertTrue(ColorFactory::create($red, $green, $blue)->scale()->isEqualTo($this->canvasImage->getPixelAt($x, $y)));
    }
}
