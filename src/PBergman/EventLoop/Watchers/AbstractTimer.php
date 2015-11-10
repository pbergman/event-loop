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
        return $this->isActive() && (double) $this->getAlarm()->format('U.u') <= (double) $this->getNewDateTime()->format('U.u');
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
        return \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true) + $offset));
    }
}