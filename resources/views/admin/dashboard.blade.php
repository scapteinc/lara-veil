@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Lara-Veil Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Plugins Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm font-semibold uppercase">Total Plugins</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['plugins']['total'] }}</p>
            <p class="text-gray-500 text-sm mt-2">{{ $stats['plugins']['active'] }} active</p>
        </div>

        <!-- Themes Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm font-semibold uppercase">Total Themes</h3>
            <p class="text-3xl font-bold text-green-600">{{ $stats['themes']['total'] }}</p>
            <p class="text-gray-500 text-sm mt-2">{{ $stats['themes']['active'] }} active</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('lara-veil.plugins.index') }}" class="block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Manage Plugins
                </a>
                <a href="{{ route('lara-veil.themes.index') }}" class="block px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Manage Themes
                </a>
                <a href="{{ route('lara-veil.media.index') }}" class="block px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                    Manage Media
                </a>
            </div>
        </div>

        <!-- System Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">System Info</h2>
            <div class="space-y-2 text-sm">
                <p><strong>Version:</strong> 2.0.0</p>
                <p><strong>PHP:</strong> {{ phpversion() }}</p>
                <p><strong>Laravel:</strong> {{ app()->version() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
