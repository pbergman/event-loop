<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Watchers\IntervalWatcher;
use PBergman\EventLoop\Watchers\SignalWatcher;

/**
 * Class SignalWatcherTest
 *
 * @package PBergman\EventLoop\Tests
 */
class SignalWatcherTest extends \PHPUnit_Framework_TestCase
{
    function testSignalWatcher()
    {
        $bool = false;
        $watcher = new SignalWatcher(SIGUSR1, function($w) use(&$bool) {
            $this->assertInstanceOf('PBergman\EventLoop\Watchers\SignalWatcher', $w);
            $this->assertInstanceOf('PBergman\EventLoop\Watchers\AbstractWatcher', $w);
            $bool = true;
        });
        $this->assertFalse($bool);
        posix_kill(posix_getpid(), SIGUSR1);
        $watcher();
        $this->assertTrue($bool);
    }
}