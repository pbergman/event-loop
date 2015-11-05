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
     * @inheritdoc
     */
    function __construct(callable $callback)
    {
        parent::__construct($callback);
    }

    /**
     * @return mixed
     */
    function __invoke()
    {
        $this->run($this);
    }
}