<?php

namespace Nette\Profiler\Model;

class Point
{
    public readonly float $start;
    public readonly float $end;

    public function __construct()
    {
        $this->start = microtime(true);
    }
    
    public function end()
    {
        $this->end = microtime(true);
    }
}
