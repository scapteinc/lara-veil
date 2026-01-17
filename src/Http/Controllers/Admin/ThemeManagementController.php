<?php

namespace Scapteinc\LaraVeil\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Scapteinc\LaraVeil\Models\Theme;

class ThemeManagementController extends Controller
{
    /**
     * List all themes (rendered by themes-gallery.volt)
     */
    public function index()
    {
        return view('lara-veil::admin.themes.index');
    }

    /**
     * Show theme details
     */
    public function show(Theme $theme)
    {
        return view('lara-veil::admin.themes.show', ['theme' => $theme]);
    }

    /**
     * Activate theme
     */
    public function activate(Theme $theme)
    {
        Theme::update(['is_active' => false]);
        $theme->update(['is_active' => true]);
        app('theme.manager')->setActive($theme->slug);

        return back()->with('success', 'Theme activated successfully');
    }

    /**
     * Show theme customization form
     */
    public function customize(Theme $theme)
    {
        return view('lara-veil::admin.themes.customize', ['theme' => $theme]);
    }

    /**
     * Update theme customization
     */
    public function updateCustomization(Request $request, Theme $theme)
    {
        $theme->update([
            'settings' => array_merge($theme->settings ?? [], $request->all()),
        ]);

        return back()->with('success', 'Theme customization updated');
    }

    /**
     * Preview theme
     */
    public function preview(Theme $theme)
    {
        return view('lara-veil::admin.themes.preview', ['theme' => $theme]);
    }

    /**
     * Show theme settings form
     */
    public function settings(Theme $theme)
    {
        return view('lara-veil::admin.themes.settings', ['theme' => $theme]);
    }

    /**
     * Update theme settings
     */
    public function updateSettings(Request $request, Theme $theme)
    {
        $theme->update([
            'settings' => $request->all(),
        ]);

        return back()->with('success', 'Theme settings updated successfully');
    }
}
