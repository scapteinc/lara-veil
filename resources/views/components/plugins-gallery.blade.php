<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Scapteinc\LaraVeil\Models\Plugin;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'plugins' => Plugin::latest()->paginate(12),
        ];
    }

    public function activate($id)
    {
        $plugin = Plugin::findOrFail($id);
        $plugin->update(['status' => 'active']);
        session()->flash('success', "{$plugin->name} activated successfully.");
    }

    public function deactivate($id)
    {
        $plugin = Plugin::findOrFail($id);
        $plugin->update(['status' => 'inactive']);
        session()->flash('success', "{$plugin->name} deactivated successfully.");
    }

    public function delete($id)
    {
        $plugin = Plugin::findOrFail($id);
        $pluginName = $plugin->name;
        $plugin->delete();
        session()->flash('success', "{$pluginName} deleted successfully.");
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Plugins</h1>
        <a href="{{ route('lara-veil.plugins.create') }}" class="lara-veil-button lara-veil-button-primary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Install Plugin
        </a>
    </div>

    @if(session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plugins as $plugin)
            <div class="lara-veil-card hover:shadow-lg transition-shadow">
                <div class="lara-veil-card-body space-y-4">
                    <!-- Header -->
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $plugin->name }}</h3>
                            <p class="text-xs text-gray-500 font-mono">{{ $plugin->namespace }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium"
                              style="background-color: {{ $plugin->status === 'active' ? '#dcfce7' : '#f3f4f6' }}; color: {{ $plugin->status === 'active' ? '#15803d' : '#6b7280' }}">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $plugin->status === 'active' ? '#22c55e' : '#9ca3af' }}"></span>
                            {{ $plugin->status_label }}
                        </span>
                    </div>

                    <!-- Description -->
                    @if($plugin->description)
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $plugin->description }}</p>
                    @endif

                    <!-- Metadata -->
                    <div class="text-xs text-gray-500 space-y-1 border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span>Version:</span>
                            <span class="font-mono">{{ $plugin->version }}</span>
                        </div>
                        @if($plugin->author)
                            <div class="flex justify-between">
                                <span>Author:</span>
                                <span>{{ $plugin->author }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Installed:</span>
                            <span>{{ $plugin->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-200 pt-4 flex gap-2">
                        <a href="{{ route('lara-veil.plugins.edit', $plugin) }}" class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-xs font-medium transition">
                            Settings
                        </a>
                        @if($plugin->status === 'active')
                            <button wire:click="deactivate({{ $plugin->id }})" class="flex-1 px-3 py-2 bg-amber-50 text-amber-600 rounded hover:bg-amber-100 text-xs font-medium transition">
                                Deactivate
                            </button>
                        @else
                            <button wire:click="activate({{ $plugin->id }})" class="flex-1 px-3 py-2 bg-green-50 text-green-600 rounded hover:bg-green-100 text-xs font-medium transition">
                                Activate
                            </button>
                        @endif
                        <button wire:click="delete({{ $plugin->id }})" wire:confirm="Delete {{ $plugin->name }}?" class="px-3 py-2 bg-red-50 text-red-600 rounded hover:bg-red-100 text-xs font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 text-center">
                <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">No plugins installed</h3>
                <p class="text-gray-600 mb-4">Get started by installing your first plugin</p>
                <a href="{{ route('lara-veil.plugins.create') }}" class="inline-block px-6 py-2 lara-veil-button lara-veil-button-primary">
                    Install Plugin
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $plugins->links() }}
    </div>
</div>
