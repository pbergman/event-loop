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
    /** @var callable */
    protected $callback;
    /** @var Loop */
    protected $loop;
    /** @var int */
    protected $state = self::STATE_ACTIVE;

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
        return $this->state === self::STATE_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->state === self::STATE_FINISHED;
    }

    /**
     * @return bool
     */
    public function isStopped()
    {
        return $this->state === self::STATE_STOPPED;
    }

    /**
     * this will stop the current watcher from being called
     * by the loop and removes the object from the queue.
     *
     * @return $this
     */
    public function finished()
    {
        $this->state = self::STATE_FINISHED;
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
        $this->state = self::STATE_STOPPED;
        return $this;
    }

    /**
     * reactivate the stopped watcher.
     *
     * @return $this
     */
    public function start()
    {
        $this->state = self::STATE_ACTIVE;
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
     * get current state of watcher should return on of STATE_* constants
     *
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function __invoke()
    {
        $this->run();
        return $this->state;
    }

    /**
     * @return null|\Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        if ($this->getLoop() instanceof Loop) {
            return $this->getLoop()->getLogger();
        } else {
            return null;
        }

    }

    /**
     * dispatch event
     */
    abstract protected function run();

}