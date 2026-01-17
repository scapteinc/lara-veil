<?php

namespace Scapteinc\LaraVeil\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Scapteinc\LaraVeil\Models\Plugin;

class PluginController extends Controller
{
    /**
     * Get all plugins
     */
    public function index()
    {
        return response()->json(Plugin::all());
    }

    /**
     * Get a specific plugin
     */
    public function show(Plugin $plugin)
    {
        return response()->json($plugin);
    }

    /**
     * Install a plugin
     */
    public function store(Request $request)
    {
        // TODO: Implement plugin installation
        return response()->json(['message' => 'Plugin installed'], 201);
    }

    /**
     * Update a plugin
     */
    public function update(Request $request, Plugin $plugin)
    {
        // TODO: Implement plugin update
        return response()->json($plugin);
    }

    /**
     * Delete a plugin
     */
    public function destroy(Plugin $plugin)
    {
        // TODO: Implement plugin uninstallation
        $plugin->delete();
        return response()->json(null, 204);
    }

    /**
     * Activate a plugin
     */
    public function activate(Plugin $plugin)
    {
        app('plugin.manager')->activate($plugin->name);
        $plugin->update(['status' => 'active']);

        return response()->json([
            'message' => 'Plugin activated',
            'plugin' => $plugin,
        ]);
    }

    /**
     * Deactivate a plugin
     */
    public function deactivate(Plugin $plugin)
    {
        app('plugin.manager')->deactivate($plugin->name);
        $plugin->update(['status' => 'inactive']);

        return response()->json([
            'message' => 'Plugin deactivated',
            'plugin' => $plugin,
        ]);
    }
}
