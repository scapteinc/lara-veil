<?php

return [
    /**
     * Plugin Configuration
     */
    'plugin' => [
        /**
         * Paths where plugins are stored
         */
        'paths' => [
            base_path('packages'),
        ],

        /**
         * Plugin caching configuration
         */
        'cache' => [
            'enabled' => env('PLUGIN_CACHE', true),
            'key' => 'lara-veil.plugins',
            'ttl' => 3600, // 1 hour
        ],

        /**
         * Auto-discovery of plugins
         */
        'auto_discover' => true,
    ],

    /**
     * Theme Configuration
     */
    'theme' => [
        /**
         * Paths where themes are stored
         */
        'paths' => [
            base_path('themes'),
        ],

        /**
         * Default theme
         */
        'default' => env('THEME_DEFAULT', 'default'),

        /**
         * Fallback theme if default is missing
         */
        'fallback' => env('THEME_FALLBACK', 'default'),
    ],

    /**
     * Hook System Configuration
     */
    'hooks' => [
        /**
         * Enable hook system
         */
        'enabled' => true,

        /**
         * Cache hook registrations
         */
        'cache' => [
            'enabled' => env('HOOK_CACHE', false),
            'ttl' => 3600,
        ],
    ],

    /**
     * Security Configuration
     */
    'security' => [
        /**
         * Require authentication for plugin/theme operations
         */
        'require_auth' => true,

        /**
         * Policy class for authorization
         */
        'policy' => null,

        /**
         * Validate plugin signatures
         */
        'validate_signatures' => true,
    ],

    /**
     * Database Configuration
     */
    'database' => [
        /**
         * Table prefixes
         */
        'tables' => [
            'plugins' => 'plugins',
            'themes' => 'themes',
            'media' => 'media',
        ],
    ],

    /**
     * Update Configuration
     */
    'updates' => [
        /**
         * Check for updates
         */
        'check_updates' => env('PLUGIN_CHECK_UPDATES', true),

        /**
         * Update channel (stable, beta, dev)
         */
        'channel' => env('UPDATE_CHANNEL', 'stable'),

        /**
         * Auto-update plugins
         */
        'auto_update' => env('PLUGIN_AUTO_UPDATE', false),

        /**
         * Backup before update
         */
        'backup_before_update' => true,
    ],
];
