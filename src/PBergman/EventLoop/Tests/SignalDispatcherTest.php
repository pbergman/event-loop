<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Watchers\CallbackWatcher;
use PBergman\EventLoop\Watchers\SignalDispatcher;
use PBergman\EventLoop\Watchers\SignalWatcher;

/**
 * Class SignalDispatcherTest
 *
 * @package PBergman\EventLoop\Tests
 */
class SignalDispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testSignalDispatcher()
    {
        $ret = 0;
        $dispatcher = new SignalDispatcher();
        $dispatcher::clear();
        new SignalWatcher(SIGUSR1, 0, function() use(&$ret) { $ret++; });
        new SignalWatcher(SIGUSR2, 0, function() use(&$ret) { $ret++; });
        $this->assertTrue($dispatcher::has(SIGUSR1));
        $this->assertTrue($dispatcher::has(SIGUSR2));
        $dispatcher::ignore(SIGUSR1);
        $this->assertTrue($dispatcher::has(SIGUSR1));
        $this->assertTrue($dispatcher::has(SIGUSR2));
        posix_kill(posix_getpid(), SIGUSR1);
        posix_kill(posix_getpid(), SIGUSR2);
        $this->assertSame(1, $ret);
        $dispatcher::remove(SIGUSR1);
        $this->assertFalse($dispatcher::has(SIGUSR1));
        $this->assertTrue($dispatcher::has(SIGUSR2));
    }

    public function testSetSignals()
    {
        $ret = 0;

        $dispatcher = new SignalDispatcher();
        $dispatcher::clear();
        new SignalWatcher(SIGCHLD, 0, function() use(&$ret) { $ret++; });
        new SignalWatcher(SIGCHLD, 0, function() use(&$ret) { $ret++; });
        $this->assertTrue($dispatcher::has(SIGCHLD));
        $this->assertSame(2, count($dispatcher));
        $dispatcher::set([
            new SignalWatcher(SIGCHLD, 0, function() use(&$ret) { $ret++; })
        ]);
        $this->assertSame(1, count($dispatcher));
    }
}
