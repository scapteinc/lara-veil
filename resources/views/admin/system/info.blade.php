@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('lara-veil.admin.dashboard') }}" class="text-blue-500 hover:text-blue-700">&larr; Back to Dashboard</a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold mb-8">System Information</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-xl font-bold mb-4">System Details</h2>
                <div class="space-y-2">
                    <p><strong>Lara-Veil Version:</strong> {{ $info['version'] }}</p>
                    <p><strong>PHP Version:</strong> {{ $info['php_version'] }}</p>
                    <p><strong>Laravel Version:</strong> {{ $info['laravel_version'] }}</p>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-bold mb-4">Statistics</h2>
                <div class="space-y-2">
                    <p><strong>Total Plugins:</strong> {{ $info['plugins_total'] }}</p>
                    <p><strong>Active Plugins:</strong> {{ $info['plugins_active'] }}</p>
                    <p><strong>Total Themes:</strong> {{ $info['themes_total'] }}</p>
                    <p><strong>Total Media:</strong> {{ $info['media_total'] }}</p>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-bold mb-4">Image Drivers</h2>
                <div class="space-y-2">
                    <p>
                        <strong>ImageMagick:</strong>
                        <span class="px-2 py-1 rounded {{ $info['drivers']['imagick'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $info['drivers']['imagick'] ? 'Available' : 'Not Available' }}
                        </span>
                    </p>
                    <p>
                        <strong>GD:</strong>
                        <span class="px-2 py-1 rounded {{ $info['drivers']['gd'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $info['drivers']['gd'] ? 'Available' : 'Not Available' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
