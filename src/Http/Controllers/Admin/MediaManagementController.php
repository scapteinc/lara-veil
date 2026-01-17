<?php

namespace Scapteinc\LaraVeil\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Scapteinc\LaraVeil\Models\Media;

class MediaManagementController extends Controller
{
    /**
     * List all media
     */
    public function index(Request $request)
    {
        $query = Media::query();

        if ($request->has('collection')) {
            $query->where('collection_name', $request->input('collection'));
        }

        if ($request->has('search')) {
            $query->where('file_path', 'like', '%' . $request->input('search') . '%');
        }

        $media = $query->paginate(20);

        return view('lara-veil::admin.media.index', ['media' => $media]);
    }

    /**
     * Show media upload form
     */
    public function uploadForm()
    {
        return view('lara-veil::admin.media.upload');
    }

    /**
     * Store uploaded media
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'collection_name' => 'nullable|string',
        ]);

        try {
            $mediaForge = app('media.forge');

            $path = $mediaForge
                ->upload($request->file('file'))
                ->run();

            $media = Media::create([
                'model_type' => $request->input('model_type', 'App\Models\File'),
                'model_id' => $request->input('model_id', 0),
                'collection_name' => $request->input('collection_name', 'default'),
                'file_path' => $path,
                'disk' => 'public',
                'mime_type' => $request->file('file')->getMimeType(),
                'size' => $request->file('file')->getSize(),
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
     * Show media details
     */
    public function show(Media $media)
    {
        return view('lara-veil::admin.media.show', ['media' => $media]);
    }

    /**
     * Show media edit form
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
            'collection_name' => 'nullable|string',
        ]);

        $media->update($request->only(['collection_name', 'metadata']));

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
            $mediaForge = app('media.forge');
            $mediaForge->delete($media->file_path, 'all');
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
