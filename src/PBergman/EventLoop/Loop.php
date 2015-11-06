<?php
/**
 * @author    Philip Bergman <pbergman@live.nl>
 * @copyright Philip Bergman
 */
namespace PBergman\EventLoop;

use PBergman\EventLoop\Watchers\WatcherInterface;
use PBergman\EventLoop\Watchers\CallbackWatcher;
use PBergman\EventLoop\Watchers\IntervalWatcher;
use PBergman\EventLoop\Watchers\PeriodicWatcher;
use PBergman\EventLoop\Watchers\ScheduledWatcher;
use PBergman\EventLoop\Watchers\SignalWatcher;

/**
 * Class Loop
 *
 * @package PBergman\EventLoop
 */
class Loop implements \Countable
{
    /** @var  \SplQueue */
    protected $queue;
    /** @var bool  */
    protected $stop = false;
    /** @var int  */
    protected $min = 200000;
    /** @var int  */
    protected $ticks = 0;

    /**
     * @inheritdoc
     */
    function __construct()
    {
        $this->queue = new \SplQueue();
    }

    /**
     * add watcher to main loop
     *
     * @param   WatcherInterface $watcher
     * @return  $this
     */
    public function add(WatcherInterface $watcher)
    {
        $this->queue->enqueue(
            $watcher->setLoop($this)
        );
        return $this;
    }

    /**
     * peek at bottom of queue
     *
     * @return mixed
     */
    public function peekBottom()
    {
        return $this->queue->bottom();

    }

    /**
     * peek at top of queue
     *
     * @return mixed
     */
    public function peekTop()
    {
        return $this->queue->top();
    }

    /**
     * do filter over the queue
     *
     * @param callable $filter
     */
    public function filter(callable $filter)
    {
        foreach ($this->getDequeuedWatcher() as $watcher) {
            if (false !== $filter($watcher)) {
                $this->queue->enqueue($watcher);
            }
        }
    }

    /**
     * return (filterd) list of watchers
     *
     * @param   callable $filter
     * @return  array
     */
    public function grep(callable $filter)
    {
        $ret = [];
        foreach ($this->getWatcher() as $watcher) {
            if (false !== $filter($watcher)) {
                $ret[] = $watcher;
            }
        }
        return $ret;
    }

    /**
     * @return WatcherInterface|WatcherInterface[]
     */
    public function getWatcher()
    {
        // not using foreach and setting count because
        // queue can change dynamically and we just
        // want loop over current queue.
        $c = $this->queue->count();
        for ($i = 0; $i < $c; $i++) {
            yield $this->queue[$i];
        }
    }

    /**
     * a generator that loops once over the dequeued and return a watcher
     *
     * @return WatcherInterface[]
     */
    protected function getDequeuedWatcher()
    {
        $c = $this->queue->count();
        for ($i = 0; $i < $c; $i++) {
            yield $this->queue->dequeue();
        }
    }

    /**
     * run main loop
     *
     * @param null $limit
     */
    public function run($limit = null)
    {
        do {
            $start = microtime(true);

            foreach ($this->getDequeuedWatcher() as $watcher) {

                switch ($watcher->getState()) {
                    case $watcher::STATE_ACTIVE:
                        if ($watcher::STATE_FINISHED !== $watcher()) {
                            $this->queue->enqueue($watcher);
                        }
                        break;
                    case $watcher::STATE_STOPPED:
                        $this->queue->enqueue($watcher);
                        break;
                }
            }


            if (0 > ($sleep = $this->min - (microtime(true) - $start) * 1000000)) {
                $sleep = abs($sleep) % $this->min;
            }

            usleep($sleep);

            $this->ticks++;

            if ($this->ticks % 100) {
                gc_collect_cycles();
            }

            if (!is_null($limit) && $limit <= $this->ticks) {
                break;
            }

        } while ($this->hasActiveWatchers() && $this->stop === false);
    }

    /**
     * check if we got active watchers
     *
     * @return bool
     */
    protected function hasActiveWatchers()
    {
        foreach ($this->getWatcher() as $watcher) {
            if ($watcher->isActive()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return $this
     */
    public function stop()
    {
        $this->stop = true;
        return $this;
    }

    /**
     * @return int
     */
    public function getTicks()
    {
        return $this->ticks;
    }

    /**
     * @param  int $min
     * @return $this;
     */
    public function setMinDuration($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * add a callback watcher
     *
     * @param   callable $callback
     * @return  $this
     */
    public function addCallback(callable $callback)
    {
        $this->add(new CallbackWatcher($callback));
        return $this;
    }

    /**
     * add a interval watcher that will called ever given $interval
     *
     * @param   double   $interval
     * @param   callable $callback
     * @return  $this
     */
    public function addInterval($interval, callable $callback)
    {
        $this->add(new IntervalWatcher($interval, $callback));
        return $this;
    }

    /**
     * set a watcher that will be called after given time (once)
     *
     * @param   \DateTime $time
     * @param   callable  $callback
     * @return  $this
     */
    public function addScheduled(\DateTime $time, callable $callback)
    {
        $this->add(new ScheduledWatcher($time, $callback));
        return $this;
    }

    /**
     * Add scheduled watcher that will start after given
     * time with given interval till stop time is reached.
     *
     * @param \DateTime $start
     * @param \DateTime $stop
     * @param double    $interval
     * @param callable $callback
     * @return $this
     */
    public function addPeriodic(\DateTime $start, \DateTime $stop, $interval, callable $callback)
    {
        $this->add(new PeriodicWatcher($start, $stop, $interval, $callback));
        return $this;
    }

    /**
     * add a signal watcher, every tick it will check for pending signals
     *
     * @param   int      $signal
     * @param   callable $callback
     * @return  $this
     */
    public function addSignal($signal, callable $callback)
    {
        $this->add(new SignalWatcher($signal, $callback));
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->queue->count();
    }

    /**
     * Rest internals
     */
    public function reset()
    {
        $this->ticks = 0;
        $this->queue = new $this->queue();
        $this->stop = false;
        $this->min = 200000;
    }
}