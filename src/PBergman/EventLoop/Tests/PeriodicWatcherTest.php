<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Watchers\PeriodicWatcher;

/**
 * Class PeriodicWatcherTest
 *
 * @package PBergman\EventLoop\Tests
 */
class PeriodicWatcherTest extends \PHPUnit_Framework_TestCase
{
    function testPeriodicWatcher()
    {
        $called = 0;
        $watcher = new PeriodicWatcher(
            PeriodicWatcher::getNewDateTime(),
            PeriodicWatcher::getNewDateTime(0.5),
            0.1,
            function($w) use (&$called) {
                $this->assertInstanceOf('PBergman\EventLoop\Watchers\PeriodicWatcher', $w);
                $this->assertInstanceOf('PBergman\EventLoop\Watchers\AbstractTimer', $w);
                $called++;
            }
        );

        for ($i = 0; $i < 15; $i++) {
            if ($watcher->isActive()) {
                $watcher();
            }
            usleep(100000);
        }

        $this->assertSame($called, 5); // 5 plus first tick
    }

    function testPeriodicWatcherStopped()
    {
        $called = 0;
        $watcher = new PeriodicWatcher(
            PeriodicWatcher::getNewDateTime(),
            PeriodicWatcher::getNewDateTime(),
            0.1,
            function($w) use (&$called) {
                $called++;
            }
        );

        for ($i = 0; $i < 15; $i++) {
            if ($watcher->isActive()) {
                $watcher();
            }
            usleep(100000);
        }

        $this->assertSame($called, 1); // 5 plus first tick
    }
}