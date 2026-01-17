@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('lara-veil.admin.dashboard') }}" class="text-blue-500 hover:text-blue-700">&larr; Back to Dashboard</a>
    </div>

    <h1 class="text-3xl font-bold mb-8">Settings</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="space-y-2">
            <a href="{{ route('lara-veil.settings.general') }}" class="block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                General
            </a>
            <a href="{{ route('lara-veil.settings.security') }}" class="block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Security
            </a>
            <a href="{{ route('lara-veil.settings.cache') }}" class="block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Cache
            </a>
        </div>

        <div class="md:col-span-3 bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-4">Settings Overview</h2>
            <p class="text-gray-600">Select a settings category from the left menu to manage your Lara-Veil configuration.</p>
        </div>
    </div>
</div>
@endsection
