<?php

use PhpRayTracer\RayTracer\Camera\Camera;
use PhpRayTracer\RayTracer\Camera\CameraFactory;
use PhpRayTracer\RayTracer\Light\Light;
use PhpRayTracer\RayTracer\Light\LightFactory;
use PhpRayTracer\RayTracer\Matrix\MatrixFactory;
use PhpRayTracer\RayTracer\Shape\Shape;
use PhpRayTracer\RayTracer\Shape\Sphere;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;
use PhpRayTracer\RayTracer\World\WorldFactory;

require '../../vendor/autoload.php';

$world = WorldFactory::create();
$world->setLight(createWorldLight());
$world->addShape(createFloor());
$world->addShape(createLeftWall());
$world->addShape(createRightWall());
$world->addShape(createLargeSphere());
$world->addShape(createSmallRightSphere());
$world->addShape(createSmallLeftSphere());

$camera = createCamera(400, 200, M_PI / 3);

$startTime = microtime(true);

$canvas = $camera->render($world);

$endTime = microtime(true);
$elapsedTime = $endTime - $startTime;

// Display the elapsed time
echo "Time to Render: " . round($elapsedTime, 4) . " seconds\n";


$ppm = $canvas->canvasToPPM();
file_put_contents('scene.ppm', $ppm);

function createFloor(): Shape
{
    $floor = new Sphere();
    $floor->setTransform(MatrixFactory::createScaling(10, 0.01, 10));
    $material = $floor->getMaterial();
    $material->setColor(ColorFactory::create(226/255,193/255,169/255));
    $material->setSpecular(0);

    return $floor;
}

function createLeftWall(): Shape
{
    $leftWall = new Sphere();
    $translation = MatrixFactory::createTranslation(0, 0, 5);
    $rotationY = MatrixFactory::createRotationY(-M_PI_4);
    $rotationX = MatrixFactory::createRotationX(M_PI_2);
    $scaling = MatrixFactory::createScaling(10, 0.01, 10);
    $leftWall->setTransform($translation->multiplyMatrix($rotationY)->multiplyMatrix($rotationX)->multiplyMatrix($scaling));
    $material = $leftWall->getMaterial();
    $material->setColor(ColorFactory::create(243/255,222/255,201/255));
    $material->setSpecular(0);

    return $leftWall;
}

function createRightWall(): Shape
{
    $rightWall = new Sphere();
    $translation = MatrixFactory::createTranslation(0, 0, 5);
    $rotationY = MatrixFactory::createRotationY(M_PI_4);
    $rotationX = MatrixFactory::createRotationX(M_PI_2);
    $scaling = MatrixFactory::createScaling(10, 0.01, 10);
    $rightWall->setTransform($translation->multiplyMatrix($rotationY)->multiplyMatrix($rotationX)->multiplyMatrix($scaling));
    $material = $rightWall->getMaterial();
    $material->setColor(ColorFactory::create(234/255,210/255,190/255));
    $material->setSpecular(0);

    return $rightWall;
}

function createLargeSphere(): Shape
{
    $largeSphere = new Sphere();
    $translation = MatrixFactory::createTranslation(-0.5, 1, 0.5);
    $largeSphere->setTransform($translation);
    $material = $largeSphere->getMaterial();
    $material->setColor(ColorFactory::create(125/255,220/255,31/255));
    $material->setDiffuse(0.7);
    $material->setSpecular(0.3);

    return $largeSphere;
}

function createSmallRightSphere(): Shape
{
    $smallRightSphere = new Sphere();
    $translation = MatrixFactory::createTranslation(1.5, 0.5, -0.5);
    $scaling = MatrixFactory::createScaling(0.5, 0.5, 0.5);
    $smallRightSphere->setTransform($translation->multiplyMatrix($scaling));
    $material = $smallRightSphere->getMaterial();
    $material->setColor(ColorFactory::create(1,165/255,0));
    $material->setDiffuse(0.7);
    $material->setSpecular(0.3);

    return $smallRightSphere;
}

function createSmallLeftSphere(): Shape
{
    $smallLeftSphere = new Sphere();
    $translation = MatrixFactory::createTranslation(-1.5, 0.33, -0.75);
    $scaling = MatrixFactory::createScaling(0.33, 0.33, 0.33);
    $smallLeftSphere->setTransform($translation->multiplyMatrix($scaling));
    $material = $smallLeftSphere->getMaterial();
    $material->setColor(ColorFactory::create(1,37/255,0));
    $material->setDiffuse(0.7);
    $material->setSpecular(0.3);

    return $smallLeftSphere;
}

function createWorldLight(): Light
{
    return LightFactory::create(TupleFactory::createPoint(-10, 10, -10), ColorFactory::create(1, 1, 1));
}

function createCamera(int $hSize, int $vSize, float $fieldOfView): Camera
{
    $camera = CameraFactory::create($hSize, $vSize, $fieldOfView);
    $camera->setTransform(MatrixFactory::createViewTransformation(
        TupleFactory::createPoint(0, 0.9, -6),
        TupleFactory::createPoint(0, 0.5, 0),
        TupleFactory::createVector(0, 1, 0)
    ));

    return $camera;
}