<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Canvas;

final class CanvasFactory
{
    public static function create(int $width, int $height): Canvas
    {
        return new Canvas($width, $height);
    }
}
