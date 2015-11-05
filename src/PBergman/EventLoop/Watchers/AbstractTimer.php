<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

/**
 * Class AbstractTimer
 *
 * @package PBergman\EventLoop\Watchers
 */
abstract class AbstractTimer extends AbstractWatcher
{
    /** @var \DateTime|null */
    protected $alarm;

    /**
     * Check if time is past the alarm time
     *
     * @return bool
     */
    protected function isValid()
    {
        return (double) $this->getAlarm()->format('U.u') <= (double) $this->getNewDateTime()->format('U.u');
    }

    /**
     * @param \DateTime $alarm
     */
    public function setAlarm(\DateTime $alarm)
    {
        $this->alarm = $alarm;
    }

    /**
     * @return \DateTime|null
     */
    public function getAlarm()
    {
        return $this->alarm;
    }

    /**
     * @return bool
     */
    public function hasAlarm()
    {
        return !is_null($this->alarm);
    }

    /**
     * return a DateTime object with microseconds
     *
     * @param   int $offset
     * @return  \DateTime
     */
    public static function getNewDateTime($offset = 0)
    {
        $time = microtime(true) + $offset;
        return new \DateTime(date(sprintf('Y-m-d H:i:s.%06d', ($time - floor($time)) * 1000000), $time));
    }
}