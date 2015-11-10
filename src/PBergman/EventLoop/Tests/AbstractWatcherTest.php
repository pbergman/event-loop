<?php
/**
 * @author    Philip Bergman <philip@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */
namespace PBergman\EventLoop\Tests;

use PBergman\EventLoop\Loop;
use PBergman\EventLoop\Watchers\AbstractWatcher;

/**
 * Class AbstractWatcherTest
 *
 * @package PBergman\EventLoop\Tests
 */
class AbstractWatcherTest extends \PHPUnit_Framework_TestCase
{
    function testAbstractWatcher()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $stub = $this->getMockForAbstractClass(AbstractWatcher::class, [function(){}]);
        $this->assertFalse($stub->isFinished());
        $this->assertTrue($stub->isActive());
        $stub->stop();
        $this->assertFalse($stub->isFinished());
        $this->assertFalse($stub->isActive());
        $stub->start();
        $this->assertFalse($stub->isFinished());
        $this->assertTrue($stub->isActive());
        $stub->finished();
        $this->assertTrue($stub->isFinished());
        $this->assertFalse($stub->isActive());
        $this->assertSame(spl_object_hash($stub), $stub->getHash());
        $this->assertNull($stub->getLogger());
        $loop = new Loop();
        $loop->setLogger($logger);
        $stub->setLoop($loop);
        $this->assertSame($loop, $stub->getLoop());
        $this->assertSame($logger, $stub->getLogger());
    }
}