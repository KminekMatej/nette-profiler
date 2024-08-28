<?php

namespace Nette\Profiler\Tests;

use Nette\Profiler\Profiler;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

class ProfilerTest extends TestCase
{
    public function testProfiler()
    {
        Environment::setup();                # inicializace Nette Tester
        Profiler::point("start");
        sleep(1);

        for ($index = 0; $index < 3; $index++) {
            $this->checkAndSleep();
        }

        $groups = Profiler::getGroups();
        self::assertDuration(1000, $groups["start"]->duration());
        self::assertDuration(3000, $groups["check"]->duration());
        self::assertDuration(2000, $groups["check-end"]->duration());
        self::assertDuration(4000, Profiler::duration());
    }

    private static function assertDuration(int $expected, int $actual, int $tolerance = 10)
    {
        if($actual > ($expected + $tolerance) || $actual < ($expected - $tolerance)){
            Assert::fail("$actual ms not in expected interval $expected ms (+- $tolerance) ", $actual, $expected);
        }
    }
    private function checkAndSleep()
    {
        Profiler::point("check");
        sleep(1);
        Profiler::point("check-end");
    }
}

(new ProfilerTest())->run();