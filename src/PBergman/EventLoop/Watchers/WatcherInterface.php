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
    const STATE_ACTIVE = 1;
    const STATE_STOPPED = 2;
    const STATE_FINISHED = 4;

    /**
     * This will be called every iteration and should hold the
     * main logic and return the current state,
     *
     * @return bool
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
     * @return bool
     */
    public function isStopped();

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
     * get current state of watcher should return on of STATE_* constants
     *
     * @return mixed
     */
    public function getState();
}