<?php

namespace Scapteinc\LaraVeil\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Show settings overview
     */
    public function index()
    {
        return view('lara-veil::admin.settings.index');
    }

    /**
     * Show general settings form
     */
    public function general()
    {
        $settings = config('lara-veil');
        return view('lara-veil::admin.settings.general', ['settings' => $settings]);
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        // TODO: Implement settings update logic
        // This would typically save to database or config file

        return back()->with('success', 'General settings updated successfully');
    }

    /**
     * Show security settings form
     */
    public function security()
    {
        $settings = config('lara-veil.security');
        return view('lara-veil::admin.settings.security', ['settings' => $settings]);
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'require_auth' => 'boolean',
            'validate_signatures' => 'boolean',
        ]);

        // TODO: Implement security settings update logic

        return back()->with('success', 'Security settings updated successfully');
    }

    /**
     * Show cache settings
     */
    public function cache()
    {
        $settings = config('lara-veil.plugin.cache');
        return view('lara-veil::admin.settings.cache', ['settings' => $settings]);
    }

    /**
     * Clear all caches
     */
    public function clearCache()
    {
        Cache::forget('lara-veil.plugins');
        Cache::forget('lara-veil.themes');
        Cache::forget('lara-veil.hooks');

        return back()->with('success', 'All caches cleared successfully');
    }
}
