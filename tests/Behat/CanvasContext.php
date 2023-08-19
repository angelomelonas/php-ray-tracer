<?php

namespace PhpRayTracer\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use LogicException;
use PhpRayTracer\RayTracer\Canvas\Canvas;
use PhpRayTracer\RayTracer\Tuple\Color;
use PhpRayTracer\RayTracer\Tuple\ColorFactory;
use PHPUnit\Framework\Assert;

final class CanvasContext implements Context
{
    private Canvas $canvas;

    /**
     * @Given /^([^"]+) is a canvas\((\d+), (\d+)\)$/
     */
    public function cIsACanvas(string $expression, int $width, int $height): void
    {
        $this->canvas = new Canvas($width, $height);
    }

    /**
     * @Then /^c\.width = (\d+)$/
     */
    public function cWidth(int $width): void
    {
        Assert::assertSame($width, $this->canvas->width);
    }

    /**
     * @Then /^c\.height = (\d+)$/
     */
    public function cHeight(int $height): void
    {
        Assert::assertSame($height, $this->canvas->height);
    }

    /**
     * @Then /^every pixel of c is color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function everyPixelOfCIsAColor(float $red, float $green, float $blue): void
    {
        foreach ($this->canvas->pixels as $row) {
            foreach ($row as $pixel) {
                Assert::assertTrue($pixel->isEqualTo(ColorFactory::create($red, $green, $blue)));
            }
        }
    }

    /**
     * @When /^write_pixel\(([^"]+), (\d+), (\d+), (red|green|blue)\)$/
     */
    public function writePixelC(string $canvas, int $row, int $column, string $colorName): void
    {
        $this->canvas->writePixel($row, $column, $this->getColorByName($colorName));
    }

    /**
     * @Then /^pixel_at\(([^"]+), (\d+), (\d+)\) = (red|green|blue)$/
     */
    public function pixelAtC(string $canvas, int $x, int $y, string $colorName): void
    {
        $expectedColor = $this->getColorByName($colorName)->scale()->clamp();
        $actualColor = $this->canvas->pixels[$y][$x];

        Assert::assertTrue($expectedColor->isEqualTo($actualColor));
    }

    /**
     * @When /^([^"]+) is a canvas_to_ppm\(([^"]+)\)$/
     */
    public function ppmIsACanvasToPPM(): void
    {
    }

    /**
     * @Then /^lines (\d+)\-(\d+) of ppm are$/
     */
    public function linesOfPPMCanvas(int $lineStart, int $lineEnd, PyStringNode $string): void
    {
        $canvasPPM = $this->canvas->canvasToPPM();
        $canvasPPMRows = explode(PHP_EOL, $canvasPPM);

        $j = 0;
        for($i = $lineStart - 1; $i < $lineEnd; $i++) {
            $expected = $string->getStrings()[$j++];
            $actual = $canvasPPMRows[$i];

            Assert::assertSame($expected, $actual);
        }
    }

    /**
     * @When /^write_pixel\(([^"]+), (\d+), (\d+), color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)\)$/
     */
    public function writePixelCAt(string $expression, int $row, int $column, float $red, float $green, float $blue): void
    {
        $color = ColorFactory::create($red, $green, $blue);
        $this->canvas->writePixel($row, $column, $color);
    }

    /**
     * @When /^every pixel of ([^"]+) is set to color\(([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+), ([-+]?\d*\.?\d+)\)$/
     */
    public function everyPixelOfCIsSetToColor(string $expression, float $red, float $green, float $blue): void
    {
        $color = ColorFactory::create($red, $green, $blue);
        $this->canvas->writeAllPixels($color);
    }

    /**
     * @Then /^([^"]+) ends with a newline character$/
     */
    public function ppmEndsWithANewlineCharacter(): void
    {
        $canvasPPM = $this->canvas->canvasToPPM();
        $canvasPPMRows = explode(PHP_EOL, $canvasPPM);

        Assert::assertSame('', $canvasPPMRows[count($canvasPPMRows) - 1] );
    }

    private function getColorByName(string $colorName): Color
    {
        if ($colorName === 'red') {
            return ColorFactory::create(1, 0, 0);
        }
        if ($colorName === 'green') {
            return ColorFactory::create(0, 1, 0);
        }
        if ($colorName === 'blue') {
            return ColorFactory::create(0, 0, 1);
        }

        throw new LogicException('Invalid color');
    }
}