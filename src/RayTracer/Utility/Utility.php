<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Utility;

use function abs;
use function bccomp;
use function getenv;
use function sprintf;
use const PHP_FLOAT_EPSILON;

final class Utility
{
    public const ROUND_TEST = 6;
    public const PRECISION_TEST = 0.000001;

    private const FORMAT_DECIMALS_6 = '%.6f';
    private const FORMAT_DECIMALS_5 = '%.5f';

    public static function areFloatsEqual(float $a, float $b): bool
    {
        if (getenv('APP_ENV') === 'test') {
            return bccomp(sprintf(self::FORMAT_DECIMALS_5, $a), sprintf(self::FORMAT_DECIMALS_5, $b), self::ROUND_TEST) === 0;
        }

        return abs($b - $a) < PHP_FLOAT_EPSILON;
    }

    public static function isFloatGreaterThanFloat(float $a, float $b): bool
    {
        return bccomp(sprintf(self::FORMAT_DECIMALS_6, $a), sprintf(self::FORMAT_DECIMALS_6, $b), self::ROUND_TEST) === 1;
    }
}
