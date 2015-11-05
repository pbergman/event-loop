<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Watchers\ScheduledWatcher;

/**
 * Class ScheduledWatcherTest
 *
 * @package PBergman\EventLoop\Tests
 */
class ScheduledWatcherTest extends \PHPUnit_Framework_TestCase
{
    function testScheduledWatcher()
    {
        $bool = false;
        $watcher = new ScheduledWatcher(ScheduledWatcher::getNewDateTime(0.5), function($w) use (&$bool) {
            $this->assertInstanceOf('PBergman\EventLoop\Watchers\ScheduledWatcher', $w);
            $this->assertInstanceOf('PBergman\EventLoop\Watchers\AbstractTimer', $w);
            $bool = true;
        });
        $this->assertTrue($watcher->hasAlarm());
        $watcher();
        $this->assertFalse($bool);
        usleep(500000);
        $watcher();
        $this->assertTrue($bool);
    }
}