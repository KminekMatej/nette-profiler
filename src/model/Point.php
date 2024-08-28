<?php

namespace Nette\Profiler\Model;

class Point
{
    /** Start time in miliseconds */
    public readonly int $start;
    /** End time in miliseconds */
    public readonly int $end;

    public function __construct()
    {
        $this->start = round(microtime(true) * 1000, 0);
    }

    public function end(bool $force = false): static
    {
        if (!isset($this->end) || $force) {
            $this->end = round(microtime(true) * 1000, 0);
        }

        return $this;
    }

    /**
     * Return current point duration in miliseconds
     *
     * @return float
     */
    public function duration(): int
    {
        $this->end();
        return $this->end - $this->start;
    }
}
