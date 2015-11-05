<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Watchers\IntervalWatcher;

/**
 * Class IntervalWatcher
 *
 * @package PBergman\EventLoop\Tests
 */
class IntervalWatcherTest extends \PHPUnit_Framework_TestCase
{
    function testIntervalWatcher()
    {
        $bool = false;

        $watcher = new IntervalWatcher(0.5, function($w) use (&$bool) {
            $this->assertInstanceOf('PBergman\EventLoop\Watchers\IntervalWatcher', $w);
            $bool = true;
        });
        $watcher();
        $this->assertFalse($bool);
        usleep(500000);
        $watcher();
        $this->assertTrue($bool);
    }
}