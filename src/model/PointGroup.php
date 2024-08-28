<?php

namespace Nette\Profiler\Model;

use ArrayAccess;
use Countable;
use JsonSerializable;
use Nette\Profiler\Profiler;
use Override;

use function count;

class PointGroup implements Countable, ArrayAccess, JsonSerializable
{
    /** @var Point[] */
    public array $points = [];
    private ?PointGroup $nextGroup = null;

    public function __construct(public readonly string $name)
    {
    }

    public function addPoint(): Point
    {
        $point = new Point();
        $this->points[] = $point;
        return $point;
    }

    public function getNextGroup(): ?PointGroup
    {
        return $this->nextGroup;
    }

    public function getFirstPoint(): ?Point
    {
        return $this->points[0] ?? null;
    }

    public function getLastPoint(): ?Point
    {
        return $this->points[array_key_last($this->points)] ?? null;
    }

    public function setNextGroup(PointGroup $nextGroup): static
    {
        $this->nextGroup = $nextGroup;
        return $this;
    }

    /**
     * Get whole group duration
     *
     * @return int
     */
    public function duration(): int
    {
        $sum = 0.0;

        foreach ($this->points as $point) {
            $sum += $point->duration();
        }

        return $sum;
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

    #[\Override]
    public function jsonSerialize(): mixed
    {
        $times = [
            "name" => $this->name,
            "complete" => $this->duration(),
            "iterations" => [],
        ];

        foreach ($this->points as $point) {
            /* @var $point Point */
            $times["iterations"][] = [
                "start" => $point->start,
                "end" => $point->end,
                "duration" => $point->duration(),
            ];
        }

        return $times;
    }
}
