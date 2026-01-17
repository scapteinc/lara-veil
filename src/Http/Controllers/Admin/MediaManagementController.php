<?php

namespace Scapteinc\LaraVeil\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Scapteinc\LaraVeil\Models\Media;

class MediaManagementController extends Controller
{
    /**
     * List all media (rendered by media-gallery-grid.volt)
     */
    public function index(Request $request)
    {
        // Volt component handles pagination and display
        return view('lara-veil::admin.media.index');
    }

    /**
     * Show media upload form (rendered by media-uploader.volt)
     */
    public function uploadForm()
    {
        return view('lara-veil::admin.media.create');
    }

    /**
     * Show media create form (alias for uploadForm)
     */
    public function create()
    {
        return view('lara-veil::admin.media.create');
    }

    /**
     * Store uploaded media
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('media', 'public');

            // Detect if it's an image
            $mimeType = $file->getMimeType();
            $mediaType = str_starts_with($mimeType, 'image/') ? 'image' : 'file';

            // Get dimensions for images
            $width = null;
            $height = null;
            if ($mediaType === 'image') {
                $dimensions = @getimagesize(storage_path('app/public/' . $path));
                if ($dimensions) {
                    $width = $dimensions[0];
                    $height = $dimensions[1];
                }
            }

            $media = Media::create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'media_type' => $mediaType,
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'disk' => 'public',
            ]);

            return redirect()
                ->route('lara-veil.media.index')
                ->with('success', 'File uploaded successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Show media details (rendered by media-editor.volt)
     */
    public function show(Media $media)
    {
        return view('lara-veil::admin.media.show', ['media' => $media]);
    }

    /**
     * Show media edit form (rendered by media-editor.volt)
     */
    public function edit(Media $media)
    {
        return view('lara-veil::admin.media.edit', ['media' => $media]);
    }

    /**
     * Update media
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'metadata' => 'nullable|json',
        ]);

        $media->update($request->only(['name', 'metadata']));

        return redirect()
            ->route('lara-veil.media.show', $media)
            ->with('success', 'Media updated successfully');
    }

    /**
     * Delete media
     */
    public function destroy(Media $media)
    {
        try {
            // Delete the file
            $fullPath = storage_path('app/public/' . $media->path);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            // Delete the record
            $media->delete();

            return redirect()
                ->route('lara-veil.media.index')
                ->with('success', 'Media deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }
}

