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
    protected static $signals;

    /**
     * add signal to listeners
     *
     * @param int               $signal
     * @param WatcherInterface $watcher
     */
    public static function add($signal, SignalWatcher $watcher)
    {
        if (!isset(self::$signals[$signal])) {
            pcntl_signal($signal, __CLASS__  . '::call');
        }

        self::$signals[$signal][] = $watcher;
    }

    /**
     * Clear all registered signals
     */
    public static function clear()
    {
        if (!empty(self::$signals)) {
            foreach(array_keys(self::$signals) as $signal) {
                self::remove($signal);
            }
        }
    }

    /**
     * remove signal from listeners and registered callbacks
     *
     * @param $signal
     */
    public static function remove($signal)
    {
        if (isset(self::$signals[$signal])) {
            pcntl_signal($signal, SIG_DFL);
            unset(self::$signals[$signal]);
        }
    }

    /**
     * Chek if a signal is registered
     *
     * @param   int $signal
     * @return  bool
     */
    public static function has($signal)
    {
        return isset(self::$signals[$signal]);
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
     * set stack of signals
     *
     * @param array $signals
     */
    public static function set(array $signals)
    {
        self::clear();

        foreach ($signals as $signal => $watchers) {
            foreach ($watchers as $watcher) {
                self::add($signal, $watcher);
            }
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
        /** @var SignalWatcher $watcher */
        foreach (self::$signals[$signal] as $watcher) {
            $watcher->execute($signal);
        }
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        $c = 0;

        foreach (self::$signals as $watchers) {
            $c += count($watchers);
        }

        return $c;
    }
}