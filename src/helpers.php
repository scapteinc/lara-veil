<?php

use Scapteinc\LaraVeil\Facades\Hook;

if (!function_exists('add_action')) {
    /**
     * Add an action hook
     *
     * @param string $hook The name of the action to hook into
     * @param callable $callback The function to execute
     * @param int $priority The priority for this action (lower runs first, default: 10)
     * @param int $acceptedArgs The number of arguments the callback accepts
     * @return void
     */
    function add_action(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        Hook::addAction($hook, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('do_action')) {
    /**
     * Execute an action hook
     *
     * @param string $hook The name of the action to execute
     * @param mixed ...$args Arguments to pass to the action callbacks
     * @return void
     */
    function do_action(string $hook, ...$args): void
    {
        Hook::doAction($hook, ...$args);
    }
}

if (!function_exists('add_filter')) {
    /**
     * Add a filter hook
     *
     * @param string $hook The name of the filter to hook into
     * @param callable $callback The function to execute
     * @param int $priority The priority for this filter (lower runs first, default: 10)
     * @param int $acceptedArgs The number of arguments the callback accepts
     * @return void
     */
    function add_filter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        Hook::addFilter($hook, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('apply_filters')) {
    /**
     * Apply a filter hook
     *
     * @param string $hook The name of the filter to apply
     * @param mixed $value The value to filter
     * @param mixed ...$args Additional arguments to pass to the filter callbacks
     * @return mixed The filtered value
     */
    function apply_filters(string $hook, $value, ...$args)
    {
        return Hook::applyFilters($hook, $value, ...$args);
    }
}

if (!function_exists('remove_hook')) {
    /**
     * Remove a hook
     *
     * @param string $type The type of hook ('action' or 'filter')
     * @param string $hook The name of the hook
     * @param callable|null $callback The specific callback to remove (if null, removes all)
     * @return void
     */
    function remove_hook(string $type, string $hook, ?callable $callback = null): void
    {
        Hook::removeHook($type, $hook, $callback);
    }
}
