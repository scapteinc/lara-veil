<?php

namespace Scapteinc\LaraVeil\Core;

/**
 * Hook System - Action/Filter implementation similar to WordPress
 */
class HookSystem
{
    protected array $hooks = [];
    protected array $executionLog = [];

    /**
     * Add an action hook
     */
    public function addAction(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): self
    {
        return $this->addHook('action', $hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Execute an action hook
     */
    public function doAction(string $hook, ...$args): void
    {
        $this->executeHook('action', $hook, $args);
    }

    /**
     * Add a filter hook
     */
    public function addFilter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): self
    {
        return $this->addHook('filter', $hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Apply a filter hook
     */
    public function applyFilters(string $hook, $value, ...$args)
    {
        return $this->executeHook('filter', $hook, [$value, ...$args], true);
    }

    /**
     * Remove a hook
     */
    public function removeHook(string $type, string $hook, ?callable $callback = null): self
    {
        // Implementation
        return $this;
    }

    /**
     * Get registered hooks
     */
    public function getRegistered(): array
    {
        return array_keys($this->hooks);
    }

    /**
     * Get execution count
     */
    public function getExecutionCount(): int
    {
        return count($this->executionLog);
    }

    protected function addHook(string $type, string $hook, callable $callback, int $priority, int $acceptedArgs): self
    {
        $key = "{$type}:{$hook}";

        if (!isset($this->hooks[$key])) {
            $this->hooks[$key] = [];
        }

        $this->hooks[$key][] = [
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $acceptedArgs,
        ];

        usort($this->hooks[$key], fn($a, $b) => $a['priority'] <=> $b['priority']);

        return $this;
    }

    protected function executeHook(string $type, string $hook, array $args = [], bool $returnValue = false)
    {
        $key = "{$type}:{$hook}";

        if (!isset($this->hooks[$key])) {
            return $returnValue ? ($args[0] ?? null) : null;
        }

        $this->executionLog[] = [
            'hook' => $key,
            'timestamp' => microtime(true),
        ];

        $value = $args[0] ?? null;

        foreach ($this->hooks[$key] as $hook) {
            if ($returnValue) {
                $value = call_user_func_array(
                    $hook['callback'],
                    array_slice($args, 0, $hook['accepted_args'])
                );
            } else {
                call_user_func_array(
                    $hook['callback'],
                    array_slice($args, 0, $hook['accepted_args'])
                );
            }
        }

        return $returnValue ? $value : null;
    }
}
