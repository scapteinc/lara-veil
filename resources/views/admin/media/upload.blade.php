@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('lara-veil.media.index') }}" class="text-blue-500 hover:text-blue-700">&larr; Back to Media</a>
    </div>

    <div class="bg-white rounded-lg shadow p-8 max-w-2xl">
        <h1 class="text-3xl font-bold mb-6">Upload File</h1>

        <form method="POST" action="{{ route('lara-veil.media.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File</label>
                <input type="file" id="file" name="file" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="collection_name" class="block text-sm font-medium text-gray-700 mb-1">Collection</label>
                <input type="text" id="collection_name" name="collection_name" placeholder="default" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('collection_name', 'default') }}">
                @error('collection_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Upload
                </button>
                <a href="{{ route('lara-veil.media.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
