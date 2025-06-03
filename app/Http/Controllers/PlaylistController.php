<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $playlists = Playlist::with('videos')->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(10);
        return view('playlists.index', compact('playlists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('playlists.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'auto_play' => 'boolean',
            'loop_playlist' => 'boolean',
            'sort_order' => 'nullable|integer'
        ]);

        Playlist::create($validated);

        return redirect()->route('playlists.index')->with('success', 'Playlist berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Playlist $playlist)
    {
        $playlist->load('videos');
        $availableVideos = Video::active()->get();
        return view('playlists.show', compact('playlist', 'availableVideos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Playlist $playlist)
    {
        return view('playlists.edit', compact('playlist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Playlist $playlist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'auto_play' => 'boolean',
            'loop_playlist' => 'boolean',
            'sort_order' => 'nullable|integer'
        ]);

        $playlist->update($validated);

        return redirect()->route('playlists.index')->with('success', 'Playlist berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Playlist $playlist)
    {
        $playlist->delete();
        return redirect()->route('playlists.index')->with('success', 'Playlist berhasil dihapus!');
    }

    /**
     * Add video to playlist
     */
    public function addVideo(Request $request, Playlist $playlist)
    {
        $validated = $request->validate([
            'video_id' => 'required|exists:videos,id',
            'sort_order' => 'nullable|integer'
        ]);

        // Check if video already exists in playlist
        if ($playlist->videos()->where('video_id', $validated['video_id'])->exists()) {
            return back()->with('error', 'Video sudah ada dalam playlist!');
        }

        $playlist->videos()->attach($validated['video_id'], [
            'sort_order' => $validated['sort_order'] ?? 0
        ]);

        return back()->with('success', 'Video berhasil ditambahkan ke playlist!');
    }

    /**
     * Remove video from playlist
     */
    public function removeVideo(Playlist $playlist, Video $video)
    {
        $playlist->videos()->detach($video->id);
        return back()->with('success', 'Video berhasil dihapus dari playlist!');
    }
}
