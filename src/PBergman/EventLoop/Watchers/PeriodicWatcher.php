<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

/**
 * Class PeriodicWatcher
 *
 * @package PBergman\EventLoop\Watchers
 */
class PeriodicWatcher extends AbstractTimer
{
    /** @var double */
    protected $interval;
    /** @var \dateTime */
    protected $stop;

    /**
     * @param \DateTime     $start
     * @param \DateTime     $stop
     * @param double        $interval
     * @param callable      $callback
     */
    function __construct(\DateTime $start, \DateTime $stop, $interval, callable $callback)
    {
        $this->interval = (double) $interval;
        $this->stop = $stop;
        $this->updateAlarm($start);
        parent::__construct($callback);
    }

    /**
     * @param null $start
     */
    protected function updateAlarm($start = null)
    {
        if (is_null($start)) {
            $start = $this->getNewDateTime();
        }

        if ((double) $this->stop->format('U.u') >= $start->format('U.u')) {
            $this->setAlarm($this->getNewDateTime($this->interval));
        } else {
            $this->finished();
        }
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