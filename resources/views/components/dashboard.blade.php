<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $title = 'Lara-Veil';
    public array $stats = [];

    public function mount()
    {
        $pluginManager = app('plugin.manager');
        $themeManager = app('theme.manager');

        $this->stats = [
            'plugins_total' => count($pluginManager->all()),
            'plugins_active' => count($pluginManager->active()),
            'themes_total' => count($themeManager->all()),
            'themes_active' => $themeManager->active(),
        ];
    }
}; ?>

<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">{{ $title }} Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">Total Plugins</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['plugins_total'] }}</p>
                <p class="text-gray-500 text-sm mt-2">{{ $stats['plugins_active'] }} active</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">Total Themes</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['themes_total'] }}</p>
                <p class="text-gray-500 text-sm mt-2">{{ $stats['themes_active'] }} active</p>
            </div>
        </div>
    </div>
</div>
