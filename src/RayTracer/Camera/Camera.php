<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Camera;

use PhpRayTracer\RayTracer\Canvas\Canvas;
use PhpRayTracer\RayTracer\Matrix\Matrix;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Ray\Ray;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\World\World;
use function tan;

final class Camera
{
    private Matrix $transform;
    private float $pixelSize;
    private float $halfWidth;
    private float $halfHeight;

    public function __construct(private readonly int $hSize, private readonly int $vSize, private readonly float $fieldOfView)
    {
        $this->transform = MatrixFactory::createIdentity();
        $this->calculatePixelSize();
    }

    public function getHSize(): int
    {
        return $this->hSize;
    }

    public function getVSize(): int
    {
        return $this->vSize;
    }

    public function getFieldOfView(): float
    {
        return $this->fieldOfView;
    }

    public function getTransform(): Matrix
    {
        return $this->transform;
    }

    public function setTransform(Matrix $transform): void
    {
        $this->transform = $transform;
    }

    public function getPixelSize(): float
    {
        return $this->pixelSize;
    }

    public function getHalfWidth(): float
    {
        return $this->halfWidth;
    }

    public function getHalfHeight(): float
    {
        return $this->halfHeight;
    }

    private function calculatePixelSize(): void
    {
        $halfView = tan($this->fieldOfView / 2);
        $aspect = $this->hSize / $this->vSize;

        if ($aspect >= 1) {
            $this->halfWidth = $halfView;
            $this->halfHeight = $halfView / $aspect;
        } else {
            $this->halfWidth = $halfView * $aspect;
            $this->halfHeight = $halfView;
        }

        $this->pixelSize = $this->halfWidth * 2 / $this->hSize;
    }

    public function rayForPixel(int $x, int $y): Ray
    {
        $xOffset = ($x + 0.5) * $this->pixelSize;
        $yOffset = ($y + 0.5) * $this->pixelSize;

        $worldX = $this->halfWidth - $xOffset;
        $worldY = $this->halfHeight - $yOffset;

        $pixel = $this->transform->inverse()->multiplyTuple(TupleFactory::createPoint($worldX, $worldY, -1));
        $origin = $this->transform->inverse()->multiplyTuple(TupleFactory::createPoint(0, 0, 0));
        $direction = $pixel->subtract($origin)->normalize();

        return new Ray($origin, $direction);
    }

    public function render(World $world): Canvas
    {
        $canvas = new Canvas($this->hSize, $this->vSize);

        for ($y = 0; $y < $this->vSize; $y++) {
            for ($x = 0; $x < $this->hSize; $x++) {
                $ray = $this->rayForPixel($x, $y);
                $color = $world->colorAt($ray);
                $canvas->writePixel($x, $y, $color);
            }
        }

        return $canvas;
    }
}
