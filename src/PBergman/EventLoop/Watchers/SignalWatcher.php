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
    /** @var int  */
    protected $signal;
    /** @var int  */
    protected $priority;

    /**
     * @param int       $signal
     * @param int       $priority
     * @param callable  $callback
     */
    public function __construct($signal, $priority = 0, callable $callback)
    {
        parent::__construct($callback);
        $this->signal = $signal;
        $this->priority = $priority;
        SignalDispatcher::add($this);
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

    /**
     * check if signal is valid
     *
     * @param   $signal
     * @return  bool
     */
    public function valid($signal)
    {
        return $signal === $this->signal && $this->isActive();
    }

    /**
     * run registered callback
     *
     * @param $signal
     */
    public function execute()
    {
        if ($this->callback) {
            call_user_func_array($this->callback, [$this]);
        }
    }

    /**
     * @return int
     */
    public function getSignal()
    {
        return $this->signal;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}