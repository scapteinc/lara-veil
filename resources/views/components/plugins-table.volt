<?php

use Livewire\Volt\Component;
use Scapteinc\LaraVeil\Models\Plugin;

new class extends Component {
    public $plugins = [];
    public $filter = 'all';

    public function mount()
    {
        $this->loadPlugins();
    }

    public function loadPlugins()
    {
        $query = Plugin::query();

        if ($this->filter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->filter === 'inactive') {
            $query->where('status', 'inactive');
        }

        $this->plugins = $query->all();
    }

    public function filterBy($status)
    {
        $this->filter = $status;
        $this->loadPlugins();
    }

    public function activate(Plugin $plugin)
    {
        app('plugin.manager')->activate($plugin->name);
        $plugin->update(['status' => 'active']);
        $this->loadPlugins();
    }

    public function deactivate(Plugin $plugin)
    {
        app('plugin.manager')->deactivate($plugin->name);
        $plugin->update(['status' => 'inactive']);
        $this->loadPlugins();
    }

    public function delete(Plugin $plugin)
    {
        $plugin->delete();
        $this->loadPlugins();
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Plugins Management</h1>
        <a href="{{ route('lara-veil.plugins.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Add Plugin
        </a>
    </div>

    <div class="flex gap-2 mb-6">
        <button
            wire:click="filterBy('all')"
            class="px-4 py-2 rounded {{ $filter === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}"
        >
            All
        </button>
        <button
            wire:click="filterBy('active')"
            class="px-4 py-2 rounded {{ $filter === 'active' ? 'bg-green-500 text-white' : 'bg-gray-200' }}"
        >
            Active
        </button>
        <button
            wire:click="filterBy('inactive')"
            class="px-4 py-2 rounded {{ $filter === 'inactive' ? 'bg-gray-500 text-white' : 'bg-gray-200' }}"
        >
            Inactive
        </button>
    </div>

    @if($plugins)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($plugins as $plugin)
                        <tr>
                            <td class="px-6 py-4">{{ $plugin->name }}</td>
                            <td class="px-6 py-4">{{ $plugin->version }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $plugin->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($plugin->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 space-x-2">
                                @if($plugin->status !== 'active')
                                    <button wire:click="activate({{ $plugin->id }})" class="text-green-500 hover:text-green-700">Activate</button>
                                @else
                                    <button wire:click="deactivate({{ $plugin->id }})" class="text-red-500 hover:text-red-700">Deactivate</button>
                                @endif
                                <button wire:click="delete({{ $plugin->id }})" wire:confirm="Are you sure?" class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">No plugins found.</p>
        </div>
    @endif
</div>
