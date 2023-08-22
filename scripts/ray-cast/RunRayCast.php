<?php

use PhpRayTracer\RayTracer\Canvas\Canvas;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Object\Sphere;
use PhpRayTracer\RayTracer\Ray\RayFactory;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;

require '../../vendor/autoload.php';

$canvasPixels = 100;
$canvas = new Canvas($canvasPixels, $canvasPixels);

$wallZ = 10;
$wallSize = 7.0;
$pixelSize = $wallSize / $canvasPixels;
$half = $wallSize / 2;

$projectionColor = ColorFactory::create(1, 0, 0);
$shape = new Sphere();
$shape->setTransform(MatrixFactory::createRotationZ(M_PI / 4)->multiplyMatrix(MatrixFactory::createScaling(0.5, 1, 1)));

$rayOrigin = TupleFactory::createPoint(0, 0, -5);
$startTime = microtime(true);
for ($y = 0; $y < $canvasPixels - 1; $y++) {
    echo sprintf('Calculating row %s/%s', $y, $canvasPixels - 1) . PHP_EOL;
    $worldY = $half - ($pixelSize * $y);
    for ($x = 0; $x < $canvasPixels - 1; $x++) {
        $worldX = -$half + ($pixelSize * $x);

        $position = TupleFactory::createPoint($worldX, $worldY, $wallZ);
        $ray = RayFactory::create($rayOrigin, ($position->subtract($rayOrigin))->normalize());
        $intersection = $shape->intersect($ray);

        if ($intersection->hit() !== null) {
            $canvas->writePixel($x, $y, $projectionColor);
        }
    }
}
$endTime = microtime(true);

$elapsedTime = $endTime - $startTime;

// Display the elapsed time
echo "Elapsed time: " . round($elapsedTime, 4) . " seconds\n";

$ppm = $canvas->canvasToPPM();
file_put_contents('ray-cast.ppm', $ppm);