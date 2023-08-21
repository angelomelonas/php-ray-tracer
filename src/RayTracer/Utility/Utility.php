<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Utility;

use function abs;
use function getenv;
use function round;
use const PHP_FLOAT_EPSILON;

final class Utility
{
    public const ROUND_TEST = 5;
    public const PRECISION_TEST = 0.000001;

    public static function areFloatsEqual(float $a, float $b): bool
    {
        if (getenv('APP_ENV') === 'test') {
            return abs(round($b, self::ROUND_TEST) - round($a, self::ROUND_TEST)) < self::PRECISION_TEST;
        }

        return abs($b - $a) < PHP_FLOAT_EPSILON;
    }
}
