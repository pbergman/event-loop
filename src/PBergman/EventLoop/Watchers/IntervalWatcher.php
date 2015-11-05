<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

/**
 * Class IntervalWatcher
 *
 * @package PBergman\EventLoop\Watchers
 */
class IntervalWatcher extends AbstractTimer
{
    /** @var double */
    protected $interval;

    /**
     * @param double    $interval
     * @param callable  $callback
     */
    function __construct($interval, callable $callback)
    {
        $this->interval = (double) $interval;
        $this->updateAlarm();
        parent::__construct($callback);
    }

    protected function updateAlarm()
    {
        $this->setAlarm(
            $this->getNewDateTime($this->interval)
        );
    }

    /**
     * @return mixed
     */
    function __invoke()
    {
        if ($this->isValid()) {
            $this->updateAlarm();
            $this->run($this);
        }
    }
}