<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop\Watchers;

/**
 * Class SignalDispatcher
 *
 * @package PBergman\EventLoop\Watchers
 */
class SignalDispatcher implements \Countable
{
    /** @var array  */
    protected static $watchers;
    /** @var bool  */
    protected static $sorted = false;

     /**
     * add signal to listeners
     *
     * @param SignalWatcher $watcher
     */
    public static function add(SignalWatcher $watcher)
    {
        pcntl_signal($watcher->getSignal(), __CLASS__  . '::call');
        self::$watchers[$watcher->getSignal()][] = $watcher;
        self::$sorted = false;
    }

    /**
     * @param   int|null $signal
     * @return  \Generator|SignalWatcher[]
     */
    protected static function getWatchers($signal)
    {
        if (false === self::$sorted) {
            foreach (self::$watchers as $signal => $watcher) {
                usort(self::$watchers[$signal], function(SignalWatcher $a, SignalWatcher $b){
                    if ($a->getPriority() == $b->getPriority()) {
                        return 0;
                    }
                    return ($a->getPriority() > $b->getPriority()) ? -1 : 1;
                });
            }
            self::$sorted = true;
        }

        foreach (self::$watchers[$signal] as $i => $watcher) {
            yield $i => $watcher;
        }
    }

    /**
     * Clear all registered signals
     */
    public static function clear()
    {
        foreach(array_keys(self::$watchers) as $signal) {
            self::remove($signal);
        }
    }

    /**
     * remove signal from listeners and registered callbacks
     *
     * @param $signal
     */
    public static function remove($signal)
    {
        foreach(self::getWatchers($signal) as $i => $watcher) {
            unset(self::$watchers[$signal][$i]);
        }
        unset(self::$watchers[$signal]);
        self::diasable($signal);
        self::$sorted = false;
    }

    /**
     * Disable signal handler
     *
     * @param   $signal
     * @return  bool
     */
    public static function diasable($signal)
    {
        return pcntl_signal($signal, SIG_DFL);
    }

    /**
     * Check if a signal is registered
     *
     * @param   int $signal
     * @return  bool
     */
    public static function has($signal)
    {
        return isset(self::$watchers[$signal]);
    }

    /**
     * set signal to ignore and keep callbacks
     *
     * @param $signal
     */
    public static function ignore($signal)
    {
        pcntl_signal($signal, SIG_IGN);
    }

    /**
     * set stack of watchers
     *
     * @param array $watchers
     */
    public static function set(array $watchers)
    {
        self::clear();

        foreach ($watchers as $watcher) {
            self::add($watcher);
        }
    }

    /**
     * Check pending signals
     */
    public static function dispatch()
    {
        pcntl_signal_dispatch();
    }

    /**
     * will
     *
     * @param int $signal
     */
    public static function call($signal)
    {
        foreach (self::getWatchers($signal) as $watcher) {
            if ($watcher->valid($signal)) {
                $watcher->execute();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        $count = 0;
        foreach (self::$watchers as $signal => $watchers) {
            $count += count($watchers);
        }
        return $count ;
    }
}