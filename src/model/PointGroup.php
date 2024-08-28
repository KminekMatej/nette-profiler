<?php

namespace Nette\Profiler\Model;

use ArrayAccess;
use Countable;
use Nette\Profiler\Profiler;
use Override;

use function count;


class PointGroup implements Countable, ArrayAccess
{
    private array $points = [];
    
    public function addPoint(): Point
    {
        $point = new Point();
        $this->points[] = $point;
        return $point;
    }

    public function getFirstPoint(): ?Point
    {
        return $this->points[0] ?? null;
    }

    public function getLastPoint(): ?Point
    {
        return $this->points[array_key_last($this->points)] ?? null;
    }

    /**
     * Get whole group duration
     *
     * @return int
     */
    public function duration(): int
    {
        if (empty($this->points)) {
            return 0.0;
        }

        $firstpoint = $this->points[0];
        $lastpoint = $this->points[array_key_last($this->points)];

        return Profiler::startToEnd($firstpoint, $lastpoint);
    }

    #[Override]
    public function count(): int
    {
        return count($this->points);
    }

    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->points[$offset]);
    }

    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->points[$offset];
    }

    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->points[$offset] = $value;
    }

    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        unset($this->points[$offset]);
    }
}
