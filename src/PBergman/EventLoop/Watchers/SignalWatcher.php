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
class SignalWatcher extends AbstractTimer
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
     * @inheritdoc
     */
    function __invoke()
    {
        SignalDispatcher::dispatch();
    }
}