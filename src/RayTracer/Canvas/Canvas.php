<?php

namespace PhpRayTracer\RayTracer\Canvas;

use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;

final class Canvas
{
    public const CHUNK_SIZE = 70;

    /** @var array<int, array<int, Color>> */
    public array $pixels = [];

    public function __construct(public int $width, public int $height)
    {
        $this->pixels = array_fill(
            0,
            $this->height,
            array_fill(0, $this->width, ColorFactory::create(0, 0, 0))
        );
    }

    public function writePixel(int $x, int $y, Color $color): void
    {
        if($x >= 0 && $x < $this->height && $y >= 0 && $y < $this->width){
            $this->pixels[$y][$x] = $color->scale()->clamp();
        }
    }

    public function writePixelCluster(int $x, int $y, Color $color): void
    {
        for ($i = $y - 1; $i <= $y + 1; $i++) {
            for ($j = $x - 1; $j <= $x + 1; $j++) {
                if($i >= 0 && $i < $this->height && $j >= 0 && $j < $this->width){
                    $this->pixels[$i][$j] = $color->scale()->clamp();
                }
            }
        }
    }

    public function writeAllPixels(Color $color): void
    {
        $this->pixels = array_fill(
            0,
            $this->height,
            array_fill(0, $this->width, $color->scale()->clamp())
        );
    }

    public function canvasToPPM(): string
    {
        $header = $this->getPPMHeader();
        $formattedLines = [];

        foreach ($this->pixels as $row) {
            $formattedRow = implode(' ', $row);
            $formattedLines[] = wordwrap($formattedRow, self::CHUNK_SIZE, PHP_EOL);
        }

        $output = implode(PHP_EOL, $formattedLines);
        return $header . $output . PHP_EOL;
    }

    private function getPPMHeader(): string
    {
        $header = "P3" . PHP_EOL;
        $header .= "{$this->width} {$this->height}" . PHP_EOL;
        $header .= "255" . PHP_EOL;

        return $header;
    }

    public function __toString(): string
    {
        $output = '';
        foreach ($this->pixels as $row) {
            $output .= implode(' ', $row) . PHP_EOL;
        }

        return $output;
    }
}