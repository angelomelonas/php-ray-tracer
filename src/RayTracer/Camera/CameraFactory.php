<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Camera;

final class CameraFactory
{
    public static function create(int $hSize, int $vSize, float $fieldOfView): Camera
    {
        return new Camera($hSize, $vSize, $fieldOfView);
    }
}
