<?php

namespace Scapteinc\LaraVeil\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Scapteinc\LaraVeil\Models\Theme;

class ThemeController extends Controller
{
    /**
     * Get all themes
     */
    public function index()
    {
        return response()->json(Theme::all());
    }

    /**
     * Get a specific theme
     */
    public function show(Theme $theme)
    {
        return response()->json($theme);
    }

    /**
     * Activate a theme
     */
    public function activate(Theme $theme)
    {
        // Deactivate all themes
        Theme::update(['is_active' => false]);

        // Activate the selected theme
        $theme->update(['is_active' => true]);
        app('theme.manager')->setActive($theme->slug);

        return response()->json([
            'message' => 'Theme activated',
            'theme' => $theme,
        ]);
    }

    /**
     * Preview a theme
     */
    public function preview(Theme $theme)
    {
        // TODO: Implement theme preview
        return response()->json([
            'message' => 'Preview mode enabled',
            'theme' => $theme,
        ]);
    }
}
