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
        foreach ($this->getQueueIterator() as $watcher) {
            if (false !== $filter($watcher)) {
                $this->queue->enqueue($watcher);
            }
        }
    }

    /**
     * a generator that loops once over the queue and return a watcher
     *
     * @return WatcherInterface[]
     */
    protected function getQueueIterator()
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
        $active = [];
        $this->iteration = 0;

        do {
            $start = microtime(true);

            foreach ($this->getQueueIterator() as $watcher) {
                if ($watcher->isActive()) {
                    $active[$watcher->getHash()] = 1;
                    $watcher();
                    if ($watcher->isFinished()) {
                        unset($active[$watcher->getHash()]);
                    } else {
                        $this->queue->enqueue($watcher);
                    }
                    if (false === $watcher->isActive()) {
                        $active[$watcher->getHash()] = 0;
                    }
                } else {
                    $active[$watcher->getHash()] = 0;
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

        } while (array_sum($active) > 0 && $this->stop === false);
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