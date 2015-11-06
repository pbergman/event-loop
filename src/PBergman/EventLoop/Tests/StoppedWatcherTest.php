<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Loop;
use PBergman\EventLoop\Watchers\CallbackWatcher;
use PBergman\EventLoop\Watchers\IntervalWatcher;
use PBergman\EventLoop\Watchers\WatcherInterface;

/**
 * Class StoppedWatcherTest
 *
 * @package PBergman\EventLoop\Tests
 */
class StoppedWatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testStoppedWatcher()
    {
        $bool = false;
        $called = 0;
        $loop = new Loop();
        $loop
            ->setMinDuration(100000)
            ->add((new CallbackWatcher(function(){}))->finished())
            ->add((new CallbackWatcher(function(CallbackWatcher $w) use (&$bool, &$called) {
                if ($called === 2) {
                    $bool = true;
                    $w->stop();
                } else {
                    $called++;
                }
            }))->stop())
            ->addInterval(0.2, function(IntervalWatcher $w){
                if ($w->getLoop()->getTicks() % 2 === 0) {
                    $stopped = $w->getLoop()->grep(function(WatcherInterface $w){
                        return $w->isStopped();
                    });
                    $stopped[0]->start();
                    $w->stop();
                }
            });

        $loop->run(100);
        $this->assertTrue($bool);
        $this->assertSame(2, $called);

        $called = 0;
        $bool = false;

        foreach ($loop->getWatcher() as $watcher) {
            if ($watcher instanceof CallbackWatcher) {
                $watcher->start();
            }
        }

        $loop->run(100);

        $this->assertTrue($bool);
        $this->assertSame(2, $called);
    }
}
