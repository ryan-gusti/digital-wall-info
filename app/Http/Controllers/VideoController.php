<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = Video::orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(10);
        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_source' => 'required|in:file,url',
            'video_url' => 'required_if:video_source,url|nullable|url',
            'video_file' => 'required_if:video_source,file|nullable|file|mimes:mp4,avi,mov,wmv,webm|max:512000', // 500MB max
            'thumbnail_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:0',
            'video_type' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer'
        ]);

        $videoData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'thumbnail_url' => $validated['thumbnail_url'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'video_type' => $validated['video_type'] ?? 'mp4',
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($validated['video_source'] === 'file' && $request->hasFile('video_file')) {
            // Handle file upload to Z: drive
            $file = $request->file('video_file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store file to Z: drive
            $filePath = $file->storeAs('videos', $fileName, 'z_drive');

            $videoData['file_path'] = $filePath;
            $videoData['storage_disk'] = 'z_drive';
            $videoData['video_url'] = null;

            // Auto-detect video type from file extension if not provided
            if (empty($validated['video_type'])) {
                $videoData['video_type'] = $file->getClientOriginalExtension();
            }
        } else {
            // Handle URL input
            $videoData['video_url'] = $validated['video_url'];
            $videoData['file_path'] = null;
            $videoData['storage_disk'] = null;
        }

        Video::create($videoData);

        return redirect()->route('videos.index')->with('success', 'Video berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        return view('videos.show', compact('video'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_source' => 'required|in:file,url',
            'video_url' => 'required_if:video_source,url|nullable|url',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,webm|max:512000', // 500MB max
            'thumbnail_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:0',
            'video_type' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer'
        ]);

        $videoData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'thumbnail_url' => $validated['thumbnail_url'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'video_type' => $validated['video_type'] ?? 'mp4',
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($validated['video_source'] === 'file') {
            if ($request->hasFile('video_file')) {
                // Delete old file if exists
                if ($video->isStoredLocally()) {
                    Storage::disk($video->storage_disk)->delete($video->file_path);
                }

                // Handle new file upload to Z: drive
                $file = $request->file('video_file');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Store file to Z: drive
                $filePath = $file->storeAs('videos', $fileName, 'z_drive');

                $videoData['file_path'] = $filePath;
                $videoData['storage_disk'] = 'z_drive';
                $videoData['video_url'] = null;

                // Auto-detect video type from file extension if not provided
                if (empty($validated['video_type'])) {
                    $videoData['video_type'] = $file->getClientOriginalExtension();
                }
            }
            // If no new file uploaded but video_source is file, keep existing file data
        } else {
            // Handle URL input - delete old file if switching from file to URL
            if ($video->isStoredLocally()) {
                Storage::disk($video->storage_disk)->delete($video->file_path);
            }

            $videoData['video_url'] = $validated['video_url'];
            $videoData['file_path'] = null;
            $videoData['storage_disk'] = null;
        }

        $video->update($videoData);

        return redirect()->route('videos.index')->with('success', 'Video berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        // Delete file if stored locally
        if ($video->isStoredLocally()) {
            Storage::disk($video->storage_disk)->delete($video->file_path);
        }

        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video berhasil dihapus!');
    }

    /**
     * Serve video files from Z: drive
     */
    public function serveFile($filename)
    {
        // Find the video by filename
        $video = Video::where('file_path', $filename)->first();

        if (!$video || !$video->isStoredLocally()) {
            abort(404, 'Video file not found');
        }

        $disk = Storage::disk($video->storage_disk);

        if (!$disk->exists($video->file_path)) {
            abort(404, 'Video file not found on disk');
        }

        $file = $disk->get($video->file_path);
        $extension = pathinfo($video->file_path, PATHINFO_EXTENSION);
        $mimeType = match(strtolower($extension)) {
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'webm' => 'video/webm',
            default => 'application/octet-stream'
        };
        $size = $disk->size($video->file_path);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', $size)
            ->header('Accept-Ranges', 'bytes')
            ->header('Cache-Control', 'public, max-age=31536000'); // Cache for 1 year
    }
}
