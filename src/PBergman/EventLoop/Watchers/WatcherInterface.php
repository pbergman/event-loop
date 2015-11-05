<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

use PBergman\EventLoop\Loop;

/**
 * Interface WatcherInterface
 *
 * @package PBergman\EventLoop\Watchers
 */
interface WatcherInterface
{
    /**
     * This will be called every iteration and should hold the main logic
     */
    public function __invoke();

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return bool
     */
    public function isFinished();

    /**
     * this will stop the current watcher from being called
     * by the loop and removes the object from the queue.
     *
     * @return $this
     */
    public function finished();

    /**
     * this will stop the current watcher from being called
     * by the loop, but the loop will keep it in the queue.
     * And can be started again by calling start().
     *
     * @return $this
     */
    public function stop();

    /**
     * reactivate the stopped watcher.
     *
     * @return $this
     */
    public function start();

    /**
     * @return Loop
     */
    public function getLoop();

    /**
     * Set the main loop
     *
     * @param  Loop $loop
     * @return $this;
     */
    public function setLoop($loop);

    /**
     * get hash of this object
     *
     * @return string
     */
    public function getHash();

    /**
     * run the registered callback
     *
     * @param       ...$args
     * @internal
     */
    public function run(...$args);
}