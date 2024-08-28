<?php

namespace Nette\Profiler;

use Nette\DI\CompilerExtension;
use Nette\Profiler\Model\Point;
use Nette\Profiler\Model\PointGroup;

use function count;

class Profiler extends CompilerExtension
{
    /** @var array{string, PointGroup} */
    private static array $groups = [];
    private static Point $runningPoint;
    private static PointGroup $runningPointGroup;

    public static function point(?string $name = null): void
    {
        $name = $name ?: "point-" . count(self::$groups);

        if (!array_key_exists($name, self::$groups)) {
            self::$groups[$name] = new PointGroup($name);

            if (isset(self::$runningPointGroup)) {
                self::$runningPointGroup->setNextGroup(self::$groups[$name]);
            }
        }

        $group = self::$runningPointGroup = self::$groups[$name];

        self::endRunningPoint();

        self::$runningPoint = $group->addPoint(); // create new Point on stack
    }

    /**
     * Ends currently running point if it exists
     *
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
    public static function getGroups(): array
    {
        self::endRunningPoint();
        return self::$groups;
    }

    /**
     * Get elapsed time in miliseconds between start of first point till end of end point
     *
     * @param Point $startPoint
     * @param Point $endPoint
     * @return int
     */
    public static function startToEnd(Point $startPoint, Point $endPoint): int
    {
        $endPoint->end();

        return $endPoint->end - $startPoint->start;
    }

    /**
     * Get elapsed time in miliseconds between start of first point till start of end point
     *
     * @param Point $startPoint
     * @param Point $endPoint
     * @return int
     */
    public static function startToStart(Point $startPoint, Point $endPoint): int
    {
        return $endPoint->start - $startPoint->start;
    }

    /**
     * Get elapsed time in miliseconds between end of first point till end of end point
     *
     * @param Point $startPoint
     * @param Point $endPoint
     * @return int
     */
    public static function endToEnd(Point $startPoint, Point $endPoint): int
    {
        $startPoint->end();
        $endPoint->end();

        return $endPoint->end - $startPoint->end;
    }

    /**
     * Get elapsed time in miliseconds between end of first point till start of end point
     *
     * @param Point $startPoint
     * @param Point $endPoint
     * @return int
     */
    public static function endToStart(Point $startPoint, Point $endPoint): int
    {
        $startPoint->end();

        return $endPoint->start - $startPoint->end;
    }

    public static function duration(): int
    {
        /* @var $firstGroup PointGroup */
        $firstGroup = self::$groups[array_key_first(self::$groups)];
        /* @var $lastGroup PointGroup */
        $lastGroup = self::$groups[array_key_last(self::$groups)];

        /* @var $firstPoint Point */
        $firstPoint = $firstGroup->getFirstPoint();
        /* @var $lastPoint Point */
        $lastPoint = $lastGroup->getLastPoint();

        return self::startToEnd($firstPoint, $lastPoint);
    }

    /**
     * Dump to file
     *
     * @param string $filename
     * @return void
     */
    public static function dumpTo(string $filename): void
    {
        $dumps = self::dump(true);
        file_put_contents($filename, $dumps);
    }

    /**
     * Dump output as an array of strings
     *
     * @return string[]|null
     */
    public static function dump(bool $return = false): array|null
    {
        $output = [
            "Nette\Profiler output",
            "---------------------",
            "Time of whole measurement: " . self::duration() . " ms",
            "Points:",
        ];

        $index = 1;
        $total = count(self::$groups);
        foreach (self::$groups as $name => $group) {
            /* @var $group PointGroup */
            $nextGroupName = $group->getNextGroup()?->name ?: "_end";

            $prefix = "[$index/$total] {$group->name} --> $nextGroupName";
            $txt = "$prefix\t..." . str_pad((string) $group->duration(), 10, " ", STR_PAD_LEFT) . " ms";
            $output[] = $txt;

            if ($group->count() > 1) { // multiple iterations
                foreach ($group->points as $iteration => $point) {
                    $output[] = "\t(Iteration " . ($iteration + 1) . ")\t..." . str_pad((string) $point->duration(), 10, " ", STR_PAD_LEFT) . " ms";
                }
            }
            $index++;
        }

        if ($return) {
            return $output;
        } else {
            foreach ($output as $line) {
                echo $line . "\n";
            }
            return null;
        }
    }
}
