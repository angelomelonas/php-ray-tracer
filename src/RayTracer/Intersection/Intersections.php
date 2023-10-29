<?php
declare(strict_types=1);

namespace PhpRayTracer\RayTracer\Intersection;

use Countable;
use function count;
use function usort;

final class Intersections implements Countable
{
    /** @param Intersection[] $intersections */
    public function __construct(private array $intersections = [])
    {
        $this->sortIntersections();
    }

    public function get(int $index): Intersection
    {
        return $this->intersections[$index];
    }

    /** @return Intersection[] */
    public function getAll(): array
    {
        return $this->intersections;
    }

    public function add(Intersection $intersection): void
    {
        $this->intersections[] = $intersection;
        $this->sortIntersections();
    }

    public function hit(): ?Intersection
    {
        foreach ($this->intersections as $intersection) {
            if ($intersection->getT() >= 0) {
                return $intersection;
            }
        }

        return null;
    }

    public function count(): int
    {
        return count($this->intersections);
    }

    private function sortIntersections(): void
    {
        usort($this->intersections, static function (Intersection $a, Intersection $b) {
            return $a->getT() <=> $b->getT();
        });
    }
}
