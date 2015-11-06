<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

/**
 * Class ScheduledWatcher
 *
 * @package PBergman\EventLoop\Watchers
 */
class ScheduledWatcher extends AbstractTimer
{
    /**
     * @param \DateTime $schedule
     * @param callable  $callback
     */
    function __construct(\DateTime $schedule, callable $callback)
    {
        $this->setAlarm($schedule);
        parent::__construct($callback);
    }

    /**
     * dispatch event
     */
    public function run()
    {
        if ($this->isValid() && is_callable($this->callback)) {
            $callabale = $this->callback;
            $callabale($this);
        }
    }
}