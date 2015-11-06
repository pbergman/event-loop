<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

/**
 * Class SignalWatcher
 *
 * @package PBergman\EventLoop\Watchers
 */
class SignalWatcher extends AbstractWatcher
{
    /**
     * @param int      $signal
     * @param callable $callback
     */
    public function __construct($signal, callable $callback)
    {
        parent::__construct($callback);
        SignalDispatcher::add($signal, $this);
    }


    /**
     * dispatch event
     */
    public function run()
    {
        if ($this->isActive()) {
            SignalDispatcher::dispatch();
        }
    }

    public function execute($signal)
    {
        if ($this->callback) {
            $callabale = $this->callback;
            $callabale($this, $signal);
        }
    }
}