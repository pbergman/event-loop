<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

use PBergman\EventLoop\Loop;

/**
 * Class AbstractWatcher
 *
 * @package PBergman\EventLoop\Watchers
 */
abstract class AbstractWatcher implements WatcherInterface
{
    /** @var bool  */
    protected $finished = false;
    /** @var bool */
    protected $isActive = true;
    /** @var callable */
    protected $callback;
    /** @var Loop */
    protected $loop;

    /**
     * @inheritdoc
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->finished;
    }

    /**
     * this will stop the current watcher from being called
     * by the loop and removes the object from the queue.
     *
     * @return $this
     */
    public function finished()
    {
        $this->finished = true;
        $this->isActive = false;
        return $this;
    }

    /**
     * this will stop the current watcher from being called
     * by the loop, but the loop will keep it in the queue.
     * And can be started again by calling start().
     *
     * @return $this
     */
    public function stop()
    {
        $this->isActive = false;
        return $this;
    }

    /**
     * reactivate the stopped watcher.
     *
     * @return $this
     */
    public function start()
    {
        $this->isActive = true;
        return $this;
    }

    /**
     * @return Loop
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * @param  Loop $loop
     * @return $this;
     */
    public function setLoop($loop)
    {
        $this->loop = $loop;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return spl_object_hash($this);
    }

    /**
     * run the registered callback
     *
     * @param ...$args
     */
    public function run(...$args)
    {
        if (is_callable($this->callback)) {
            $callabale = $this->callback;
            $callabale(...$args);
        }
    }
}