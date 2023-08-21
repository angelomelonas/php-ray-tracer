<?php

use PhpRayTracer\RayTracer\Canvas\Canvas;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\Tuple;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;

require '../../vendor/autoload.php';

$width = 800;
$height = 800;
$radius = $width / 2;
$canvas = new Canvas($width, $height);


$hours = 12;
writeCenterOfClock($canvas);

$x = 0;
$y = 0;
$z = 1;

for ($i = 0; $i < 12; $i++) {
    $rotation = MatrixFactory::createRotationY($i * (M_PI / 6));
    $scale = MatrixFactory::createScaling(0.8, 1, 0.8);

    $point = $rotation->multiplyTuple(TupleFactory::createPoint($x, $y, $z));
    $point = $scale->multiplyTuple($point);
    $point = TupleFactory::createPoint($point->x * $radius, $point->y, $point->z * $radius)->add(TupleFactory::createPoint(0, 0, 0));

    writePixel($canvas, $point);
}

$ppm = $canvas->canvasToPPM();
file_put_contents('clock.ppm', $ppm);

function writeCenterOfClock(Canvas $canvas): void
{
    $point = TupleFactory::createPoint(0, 0, 0);
    writePixel($canvas, $point);
}

function writePixel(Canvas $canvas, Tuple $point): void
{
    $canvas->writePixelCluster(
        intval(($canvas->width / 2) - $point->x),
        intval(($canvas->height / 2) - $point->z),
        ColorFactory::create(255, 255, 255)
    );
}