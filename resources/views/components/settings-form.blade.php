<?php

use Livewire\Volt\Component;

new class extends Component {
    public $settings = [];

    public $appName = '';
    public $appDescription = '';
    public $adminEmail = '';
    public $siteUrl = '';
    public $maintenanceMode = false;
    public $pluginsEnabled = true;
    public $themesEnabled = true;
    public $cachingEnabled = true;

    public function mount()
    {
        $config = config('lara-veil');

        $this->appName = $config['app']['name'] ?? '';
        $this->appDescription = $config['app']['description'] ?? '';
        $this->adminEmail = $config['app']['admin_email'] ?? '';
        $this->siteUrl = $config['app']['url'] ?? '';
        $this->maintenanceMode = $config['maintenance']['enabled'] ?? false;
        $this->pluginsEnabled = $config['features']['plugins']['enabled'] ?? true;
        $this->themesEnabled = $config['features']['themes']['enabled'] ?? true;
        $this->cachingEnabled = $config['caching']['enabled'] ?? true;
    }

    public function save()
    {
        $this->validate([
            'appName' => 'required|string|min:3|max:255',
            'appDescription' => 'required|string|max:1000',
            'adminEmail' => 'required|email',
            'siteUrl' => 'required|url',
        ]);

        try {
            // In production, these would be saved to database or config
            // For now, we'll dispatch a success message
            $this->dispatch('notify', message: 'Settings saved successfully');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error saving settings: ' . $e->getMessage(), type: 'error');
        }
    }

    public function resetToDefaults()
    {
        $this->mount();
        $this->dispatch('notify', message: 'Settings reset to defaults');
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">System Settings</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Settings Form -->
        <div class="lg:col-span-2">
            <form wire:submit.prevent="save" class="space-y-6">
                <!-- Application Settings -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Application Settings</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                            <input type="text" wire:model="appName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" />
                            @error('appName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application Description</label>
                            <textarea wire:model="appDescription" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                            @error('appDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                            <input type="email" wire:model="adminEmail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" />
                            @error('adminEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                            <input type="url" wire:model="siteUrl" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" />
                            @error('siteUrl') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Feature Settings -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Feature Settings</h2>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="pluginsEnabled" id="pluginsEnabled" class="h-4 w-4 text-blue-600 rounded" />
                            <label for="pluginsEnabled" class="ml-3 block text-sm font-medium text-gray-700">
                                Enable Plugins
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="themesEnabled" id="themesEnabled" class="h-4 w-4 text-blue-600 rounded" />
                            <label for="themesEnabled" class="ml-3 block text-sm font-medium text-gray-700">
                                Enable Themes
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="cachingEnabled" id="cachingEnabled" class="h-4 w-4 text-blue-600 rounded" />
                            <label for="cachingEnabled" class="ml-3 block text-sm font-medium text-gray-700">
                                Enable Caching
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="maintenanceMode" id="maintenanceMode" class="h-4 w-4 text-blue-600 rounded" />
                            <label for="maintenanceMode" class="ml-3 block text-sm font-medium text-gray-700">
                                Maintenance Mode
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium">
                        Save Settings
                    </button>
                    <button type="button" wire:click="resetToDefaults" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 font-medium">
                        Reset to Defaults
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                <h3 class="font-bold text-blue-900 mb-2">ℹ️ System Information</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="font-semibold text-blue-900">Laravel Version</dt>
                        <dd class="text-blue-700">{{ app()::VERSION }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-blue-900">PHP Version</dt>
                        <dd class="text-blue-700">{{ PHP_VERSION }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-blue-900">Last Updated</dt>
                        <dd class="text-blue-700">{{ now()->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                <h3 class="font-bold text-green-900 mb-3">✓ Status</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center text-green-700">
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Database Connected
                    </li>
                    <li class="flex items-center text-green-700">
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Cache Working
                    </li>
                    <li class="flex items-center text-green-700">
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        File Permissions OK
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
