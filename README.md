##EventLoop

This a simple light weight (synchronous) event loop, it uses watchers that can be registered and those will be called 
every tick (iteration). I have started this project because i needed something like this for a fork application that 
i was working on and tried ev but was missing some things and didn`t really work for me. The loop will run until there 
are no more active watchers, is stopped or is limited by the given argument of the run method. The order you add
watcher is the order the going to be called (FIFO). If the watcher is stopped and the loop will keep it in the queue 
so it can be started later. If you call the finished method the watcher will removed from the queue.

##Loop

This is the main class where you can register the watchers and holds the loop logic. The loop is build around 
the SplQueue and evey watcher in enqueued and dequeued in the tick. When the watcher is not marked as finished 
it will be enqueued again and if marked as stopped it not be called again until it is started again.

####add(WatcherInterface $watcher)

This method can be used to register you custom watcher. A custom watcher should implement WatcherInterface or 
extend AbstractWatcher.

####peekBottom()

Get the first registered watcher.

####peekTop()

Get the last registered watcher.

####filter(callable $filter)

Do filter over the registered watchers (similar as array filter).

####run(<int> $limit = null)

This will run the loop, if no limit is given it will run till all watcher are finished.

####stop()

This will stop the loop from running.

####getTicks()

Get the ticks (iteration).

####setMinDuration()

The minimal duration, if a tick was shorter or longer thant this given value it will 
sleep till next tick. Default it is 0.2 second.

####addCallback(callable $callback)

Add a callback watcher, this callback will be called every tick.

```
$loop = new Loop();
$loop->addCallback(function(CallbackWatcher $w){
    if ($w->getLoop()->getTicks() === 3) {
        $w->stopped();
    }
});

```

####addInterval(<double> $interval, callable $callback)

Add a interval watcher, if interval for example 0.4 given and min duration is 0.2 
the given callback will be called every 2 ticks.

```
$loop = new Loop();
$loop->addInterval(function(IntervalWatcher $w){
    if ($w->getLoop()->getTicks() === 3) {
        $w->getLoop()->stop();
    }
});

```

####addScheduled(\DateTime $time, callable $callback)

Add a scheduled watcher, this will be called every tick when given time is past.

```
$loop = new Loop();
$loop->addScheduled(ScheduledWatcher::getNewDateTime(2), function(ScheduledWatcher $w){
        // Will be called after 2 seconds
});

```

####addPeriodic(\DateTime $start, \DateTime $stop, <double> $interval, callable $callback)

Add a periodic watcher, this callback will be called every given interval after given start
date and will end when stop time is reached.

```
$loop = new Loop();
$loop->addPeriodic(
    ScheduledWatcher::getNewDateTime(2), 
    ScheduledWatcher::getNewDateTime(4), 
    0.4, 
    function(ScheduledWatcher $w){
        // Will be called after 2 seconds and called every 2 ticks and after 2 seconds
    }
);

```

####addSignal($signal, callable $callback)

Add a signal watcher, the given callback will be called when i signal if send to this process.

```
$loop = new Loop();
$loop->addSignal(SIGCHLD, function(SignalWatcher $w, $signal){
        echo "Got ", $signal, "\n";
});

```

####count()

Returns the count of registered watchers.

####reset()

Will do reset on the internals.