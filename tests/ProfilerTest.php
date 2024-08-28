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
        sleep(3);

        for ($index = 0; $index < 3; $index++) {
            $this->checkAndSleep();
        }

        $points = Profiler::getPoints();
        var_dump($points);
        Assert::true(false);
    }

    private function checkAndSleep()
    {
        Profiler::point("check");
        sleep(1);
        Profiler::point("check-end");
    }
}

(new ProfilerTest())->run();