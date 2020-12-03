<?php


namespace cmever\AWS\Common\traits;


trait EventHandlerTrait
{
    protected static $watchers = [];

    /**
     * listen event with trigger callback
     * @param string $event
     * @param callable $callback
     * @return bool
     */
    public static function addEventListener(string $event, callable $callback): bool
    {
        return true;
    }

    /**
     * remove event with callback
     * @param string $event
     * @param callable $callback
     * @return bool
     */
    public static function removeEventListener(string $event, callable $callback): bool
    {
        return true;
    }

    /**
     * trigger event with data
     * @param string $event
     * @param array $data
     * @return bool
     */
    public static function triggerEvent(string $event, array $data): bool
    {
        return true;
    }

    /**
     * clear event listener by event
     * @param string $event
     * @return bool
     */
    public static function clearEventListener(string $event): bool
    {
        return true;
    }
}