<?php

namespace Nette\Profiler;

use Nette\DI\CompilerExtension;
use Nette\Profiler\Model\Point;

use function count;

class Profiler extends CompilerExtension
{
    /** @var array{string, Point[]} */
    private static array $points = [];
    private static Point $runningPoint;

    public static function point(?string $name = null): void
    {
        $name = $name ?: "point-" . count(self::$points);

        if (!array_key_exists($name, self::$points)) {
            self::$points[$name] = [];
        }

        //end previously running point
        self::endRunningPoint();

        self::$points[$name][] = self::$runningPoint = new Point(); // create new Point on stack
    }

    /**
     * Ends currently running point if it exists
     * @return void
     */
    private static function endRunningPoint(): void
    {
        if (isset(self::$runningPoint)) {
            self::$runningPoint->end();
        }
    }

    /**
     * Return measured points as an array
     *
     * @return array{string, Point[]}
     */
    public static function getPoints(): array
    {
        self::endRunningPoint();
        return self::$points;
    }
}
