<?php

use PhpRayTracer\RayTracer\Canvas\Canvas;
use PhpRayTracer\RayTracer\Light\LightFactory;
use PhpRayTracer\RayTracer\Material\MaterialFactory;
use PhpRayTracer\RayTracer\Object\Sphere;
use PhpRayTracer\RayTracer\Ray\RayFactory;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;

require '../../vendor/autoload.php';

$canvasPixels = 100;
$canvas = new Canvas($canvasPixels, $canvasPixels);

$wallZ = 8;
$wallSize = 7.0;
$pixelSize = $wallSize / $canvasPixels;
$half = $wallSize / 2;


$shape = new Sphere();
$material = MaterialFactory::create();
$material->color = ColorFactory::create(0, 0.9, 0.1);
$shape->setMaterial($material);


$lightPosition = TupleFactory::createPoint(-10, 10, -10);
$lightColor = ColorFactory::create(1, 1, 1);
$light = LightFactory::create($lightPosition, $lightColor);

//$shape->setTransform(MatrixFactory::createRotationZ(M_PI / 4)->multiplyMatrix(MatrixFactory::createScaling(0.5, 1, 1)));

//$projectionColor = ColorFactory::create(1, 0, 0);
$rayOrigin = TupleFactory::createPoint(0, 0, -5);
$startTime = microtime(true);
for ($y = 0; $y < $canvasPixels - 1; $y++) {
    echo sprintf('Calculating row %s/%s', $y + 1, $canvasPixels) . PHP_EOL;
    $worldY = $half - ($pixelSize * $y);
    for ($x = 0; $x < $canvasPixels - 1; $x++) {
        $worldX = -$half + ($pixelSize * $x);

        $position = TupleFactory::createPoint($worldX, $worldY, $wallZ);
        $ray = RayFactory::create($rayOrigin, ($position->subtract($rayOrigin))->normalize());
        $intersection = $shape->intersect($ray);

        if ($intersection->hit() !== null) {
            $point = $ray->position($intersection->hit()->getT());
            $normal = $intersection->hit()->getObject()->normalAt($point);
            $eye = $ray->direction->negate();
            $color = $intersection->hit()->getObject()->getMaterial()->lighting($light, $point, $eye, $normal);

            $canvas->writePixel($x, $y, $color);
        }
    }
}
$endTime = microtime(true);

$elapsedTime = $endTime - $startTime;

// Display the elapsed time
echo "Elapsed time: " . round($elapsedTime, 4) . " seconds\n";

$ppm = $canvas->canvasToPPM();
file_put_contents('ray-cast.ppm', $ppm);