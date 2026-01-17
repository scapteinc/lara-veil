<?php

namespace Scapteinc\LaraVeil\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Scapteinc\LaraVeil\Models\Plugin;
use Scapteinc\LaraVeil\Models\Theme;
use Scapteinc\LaraVeil\Models\Media;

class SystemController extends Controller
{
    /**
     * Show system information
     */
    public function info()
    {
        $info = [
            'version' => '2.0.0',
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'plugins_total' => Plugin::count(),
            'plugins_active' => Plugin::where('status', 'active')->count(),
            'themes_total' => Theme::count(),
            'media_total' => Media::count(),
            'drivers' => [
                'imagick' => extension_loaded('imagick'),
                'gd' => extension_loaded('gd'),
            ],
        ];

        return view('lara-veil::admin.system.info', ['info' => $info]);
    }

    /**
     * Show registered hooks
     */
    public function hooks()
    {
        $hookSystem = app('hook');
        $hooks = $hookSystem->getRegistered();
        $executionCount = $hookSystem->getExecutionCount();

        return view('lara-veil::admin.system.hooks', [
            'hooks' => $hooks,
            'executionCount' => $executionCount,
        ]);
    }

    /**
     * Show system diagnostics
     */
    public function diagnostics()
    {
        $diagnostics = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkStorage(),
            'permissions' => $this->checkPermissions(),
            'extensions' => $this->checkExtensions(),
        ];

        return view('lara-veil::admin.system.diagnostics', ['diagnostics' => $diagnostics]);
    }

    /**
     * Show system logs
     */
    public function logs(Request $request)
    {
        $logFile = $request->input('file', 'lara-veil');
        $lines = 100;

        // TODO: Implement log viewing

        return view('lara-veil::admin.system.logs', [
            'logFile' => $logFile,
            'logs' => [],
        ]);
    }

    protected function checkDatabase()
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function checkStorage()
    {
        $storagePath = storage_path();
        $writable = is_writable($storagePath);

        return [
            'status' => $writable ? 'ok' : 'warning',
            'message' => $writable ? 'Storage directory is writable' : 'Storage directory is not writable',
        ];
    }

    protected function checkPermissions()
    {
        $paths = [
            'storage' => storage_path(),
            'bootstrap/cache' => bootstrap_path('cache'),
        ];

        $results = [];
        foreach ($paths as $name => $path) {
            $results[$name] = is_writable($path) ? 'ok' : 'warning';
        }

        return $results;
    }

    protected function checkExtensions()
    {
        return [
            'imagick' => extension_loaded('imagick') ? 'ok' : 'missing',
            'gd' => extension_loaded('gd') ? 'ok' : 'missing',
            'json' => extension_loaded('json') ? 'ok' : 'missing',
        ];
    }
}
