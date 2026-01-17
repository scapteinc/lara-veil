<?php

namespace Scapteinc\LaraVeil\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Scapteinc\LaraVeil\Models\Plugin;

class PluginManagementController extends Controller
{
    /**
     * List all plugins
     */
    public function index()
    {
        $plugins = Plugin::all();
        return view('lara-veil::admin.plugins.index', ['plugins' => $plugins]);
    }

    /**
     * Show create plugin form
     */
    public function create()
    {
        return view('lara-veil::admin.plugins.create');
    }

    /**
     * Store new plugin
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:plugins',
            'namespace' => 'required|string|unique:plugins',
            'version' => 'required|string',
        ]);

        $plugin = Plugin::create($request->all());

        return redirect()
            ->route('lara-veil.plugins.index')
            ->with('success', 'Plugin created successfully');
    }

    /**
     * Show edit plugin form
     */
    public function edit(Plugin $plugin)
    {
        return view('lara-veil::admin.plugins.edit', ['plugin' => $plugin]);
    }

    /**
     * Update plugin
     */
    public function update(Request $request, Plugin $plugin)
    {
        $request->validate([
            'name' => "required|string|unique:plugins,name,{$plugin->id}",
            'version' => 'required|string',
        ]);

        $plugin->update($request->only(['name', 'version']));

        return redirect()
            ->route('lara-veil.plugins.index')
            ->with('success', 'Plugin updated successfully');
    }

    /**
     * Delete plugin
     */
    public function destroy(Plugin $plugin)
    {
        $plugin->delete();

        return redirect()
            ->route('lara-veil.plugins.index')
            ->with('success', 'Plugin deleted successfully');
    }

    /**
     * Activate plugin
     */
    public function activate(Plugin $plugin)
    {
        app('plugin.manager')->activate($plugin->name);
        $plugin->update(['status' => 'active']);

        return back()->with('success', 'Plugin activated successfully');
    }

    /**
     * Deactivate plugin
     */
    public function deactivate(Plugin $plugin)
    {
        app('plugin.manager')->deactivate($plugin->name);
        $plugin->update(['status' => 'inactive']);

        return back()->with('success', 'Plugin deactivated successfully');
    }

    /**
     * Show plugin settings form
     */
    public function settings(Plugin $plugin)
    {
        return view('lara-veil::admin.plugins.settings', ['plugin' => $plugin]);
    }

    /**
     * Update plugin settings
     */
    public function updateSettings(Request $request, Plugin $plugin)
    {
        $plugin->update([
            'settings' => $request->all(),
        ]);

        return back()->with('success', 'Plugin settings updated successfully');
    }
}
