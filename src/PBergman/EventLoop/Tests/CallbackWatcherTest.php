<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Watchers\CallbackWatcher;

/**
 * Class CallbackWatcherTest
 *
 * @package PBergman\EventLoop\Tests
 */
class CallbackWatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testCallbackWatcher()
    {
        $bool = false;

        $watcher = new CallbackWatcher(function($w) use (&$bool){
            $this->assertInstanceOf('PBergman\EventLoop\Watchers\CallbackWatcher', $w);
            $bool = true;
        });
        $this->assertTrue(is_callable($watcher));
        $watcher();
        $this->assertTrue($bool);
    }
}