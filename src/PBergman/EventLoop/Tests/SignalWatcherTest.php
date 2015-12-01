<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Watchers\IntervalWatcher;
use PBergman\EventLoop\Watchers\SignalDispatcher;
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
        $watcher = new SignalWatcher(SIGUSR1, 0, function($w) use(&$bool) {
            $this->assertInstanceOf('PBergman\EventLoop\Watchers\SignalWatcher', $w);
            $this->assertInstanceOf('PBergman\EventLoop\Watchers\AbstractWatcher', $w);
            $bool = true;
        });
        $this->assertFalse($bool);
        posix_kill(posix_getpid(), SIGUSR1);
        $watcher();
        $this->assertTrue($bool);
    }

    function testSignalPriorityWatcher()
    {
        $c = 0;
        SignalDispatcher::clear();
        $watcher1 = new SignalWatcher(SIGUSR1, 0, function($w) use(&$c) {
            $c++;
            $this->assertSame(3, $c);
        });
        $watcher2 = new SignalWatcher(SIGUSR1, 2, function($w) use(&$c) {
            $c++;
            $this->assertSame(1, $c);
        });
        $watcher3 = new SignalWatcher(SIGUSR1, 1, function($w) use(&$c) {
            $c++;
            $this->assertSame(2, $c);
        });
        $this->assertSame(0, $c);
        posix_kill(posix_getpid(), SIGUSR1);
        SignalDispatcher::dispatch();
        $this->assertSame(3, $c);
    }
}