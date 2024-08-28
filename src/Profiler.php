<?php

namespace Nette\Profiler;

use Nette\DI\CompilerExtension;
use Nette\Profiler\Model\Point;

use function count;

class Profiler extends CompilerExtension
{
    private static array $points = [];
    private static Point $runningPoint;

    public static function point(?string $name = null): static
    {
        $name = $name ?: "point-" . count(self::$points);

        if (!array_key_exists($name, self::$points)) {
            self::$points[$name] = [];
        }

        //end previously running point
        if (isset(self::$runningPoint)) {
            self::$runningPoint->end();
        }

        self::$points[$name][] = self::$runningPoint = new Point(); // create new Point on stack
    }
}
