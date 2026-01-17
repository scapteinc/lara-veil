<?php

namespace Scapteinc\LaraVeil\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $pluginManager = app('plugin.manager');
        $themeManager = app('theme.manager');

        $stats = [
            'plugins' => [
                'total' => count($pluginManager->all()),
                'active' => count($pluginManager->active()),
            ],
            'themes' => [
                'total' => count($themeManager->all()),
                'active' => $themeManager->active(),
            ],
        ];

        return view('lara-veil::admin.dashboard', [
            'stats' => $stats,
        ]);
    }
}
