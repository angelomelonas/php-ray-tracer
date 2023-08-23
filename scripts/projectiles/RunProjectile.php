<?php

use PhpRayTracer\RayTracer\Canvas\Canvas;
use PhpRayTracer\RayTracer\Projectiles\Environment;
use PhpRayTracer\RayTracer\Projectiles\Projectile;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PhpRayTracer\RayTracer\Tuple\TupleFactory;

require '../../vendor/autoload.php';

$projectile = new Projectile(
    TupleFactory::createPoint(0, 1, 0),
    TupleFactory::createVector(1, 1.8, 0)->normalize()->multiply(11.25)
);

$environment = new Environment(
    TupleFactory::createVector(0, -0.1, 0),
    TupleFactory::createVector(-0.01, 0, 0)
);

$width = 900;
$height = 550;
$canvas = new Canvas($width, $height);

$ticks = 0;
while (true) {
    $canvas->writePixel(
        intval($projectile->position->x),
        intval($height - $projectile->position->y),
        ColorFactory::create(1, 1, 1)
    );
    $projectile = tick($projectile, $environment);

    echo sprintf("Projectile position: %s" . PHP_EOL, $projectile->position->__toString());

    if ($projectile->position->y < 0) {
        echo sprintf(PHP_EOL . "Projectile hit the ground at position %s after %s seconds." . PHP_EOL, $projectile->position->__toString(), $ticks++);
        break;
    }
}

$ppm = $canvas->canvasToPPM();
file_put_contents('projectile.ppm', $ppm);

function tick(Projectile $projectile, Environment $environment): Projectile
{
    $newPosition = $projectile->position->add($projectile->velocity);
    $newVelocity = $projectile->velocity->add($environment->gravity->add($environment->wind));

    return new Projectile($newPosition, $newVelocity);
}