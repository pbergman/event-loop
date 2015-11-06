<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

/**
 * Class CallbackWatcher
 *
 * @package PBergman\EventLoop\Watchers
 */
class CallbackWatcher extends AbstractWatcher
{
    /**
     * dispatch event
     */
    public function run()
    {
        if ($this->isActive() && is_callable($this->callback)) {
            $callabale = $this->callback;
            $callabale($this);
        }
    }
}