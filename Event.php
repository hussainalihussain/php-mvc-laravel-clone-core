<?php

namespace hussainalihussain\phpmvclaravelclonecore;

abstract class Event
{
    public const EVENT_BEFORE_REQUEST = 'beforeRequest';
    public const EVENT_AFTER_REQUEST  = 'afterRequest';
    public const EVENT_ERROR_OCCUR    = 'errorOccur';
    protected $eventListeners = [];

    public function on(string $eventName, $callback)
    {
        $this->eventListeners[$eventName][] = $callback;
    }

    public function trigger(string $eventName)
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];

        foreach ($callbacks as $callback)
        {
            call_user_func($callback);
        }
    }
}