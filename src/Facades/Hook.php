<?php

namespace Scapteinc\LaraVeil\Facades;

use Illuminate\Support\Facades\Facade;
use Scapteinc\LaraVeil\Core\HookSystem;

/**
 * @method static self addAction(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1)
 * @method static void doAction(string $hook, ...$args)
 * @method static self addFilter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1)
 * @method static mixed applyFilters(string $hook, $value, ...$args)
 * @method static self removeHook(string $type, string $hook, ?callable $callback = null)
 * @method static array getRegistered()
 * @method static int getExecutionCount()
 */
class Hook extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'hook';
    }
}
