<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Loop;
use PBergman\EventLoop\Watchers\CallbackWatcher;

class LoopTest extends \PHPUnit_Framework_TestCase
{
    function testLoop()
    {
        $callable = function(){};
        $loop = new Loop();
        $loop->addCallback($callable);
        $loop->addInterval(0.1,$callable);
        $loop->addPeriodic(new \DateTime(), new \DateTime(), 1, $callable);
        $loop->addSignal(SIGCHLD,$callable);
        $loop->addScheduled(new \DateTime(),$callable);
        $this->assertSame(count($loop), 5);
        $this->assertInstanceOf('PBergman\EventLoop\Watchers\CallbackWatcher', $loop->peekBottom());
        $this->assertInstanceOf('PBergman\EventLoop\Watchers\ScheduledWatcher', $loop->peekTop());
        $loop->filter(function($w){
            return $w instanceof \PBergman\EventLoop\Watchers\CallbackWatcher;
        });
        $this->assertSame(count($loop), 1);

        $loop->run(2);
        $this->assertSame($loop->getTicks(), 2);
    }

    function testLoopRun()
    {
        $loop = new Loop();
        $loop->add(new CallbackWatcher(function(){}));
        $loop->run(2);
        $this->assertSame($loop->getTicks(), 2);

        $c = 0;
        $loop->filter(function(){ return false; });
        $loop->setMinDuration(100);
        $loop->add(new CallbackWatcher(function($w) use (&$c){
            usleep(101);
            $w->getLoop()->stop();
            $c++;
        }));
        $this->assertSame(0, $c);
        $loop->run();
        $this->assertSame(1, $c);



        $a = 0;
        $b = 0;
        $loop->reset();
        $loop->setMinDuration(100);
        $loop->add(new CallbackWatcher(function($w) use (&$a){
            if ($w->getLoop()->getTicks() === 3) {
                $w->stop();
            } else {
                $a++;
            }
        }));
        $loop->add(new CallbackWatcher(function($w) use (&$b){
            if ($w->getLoop()->getTicks() === 5) {
                $w->finished();
            } else {
                $b++;
            }
        }));
        $loop->run();
        $this->assertSame(3, $a);
        $this->assertSame(5, $b);
    }
}