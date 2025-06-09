<?php

namespace App\Http\Controllers;

use App\Models\Tv;
use App\Models\Playlist;
use Illuminate\Http\Request;

class TvController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tvs = Tv::with('playlist')->orderBy('name')->paginate(10);
        return view('tvs.index', compact('tvs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $playlists = Playlist::active()->orderBy('name')->get();
        return view('tvs.create', compact('playlists'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:tvs,ip_address',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'playlist_id' => 'nullable|exists:playlists,id',
            'is_active' => 'boolean'
        ]);

        Tv::create($validated);

        return redirect()->route('tvs.index')->with('success', 'TV berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tv $tv)
    {
        $tv->load('playlist.videos');
        return view('tvs.show', compact('tv'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tv $tv)
    {
        $playlists = Playlist::active()->orderBy('name')->get();
        return view('tvs.edit', compact('tv', 'playlists'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tv $tv)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:tvs,ip_address,' . $tv->id,
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'playlist_id' => 'nullable|exists:playlists,id',
            'is_active' => 'boolean'
        ]);

        $tv->update($validated);

        return redirect()->route('tvs.index')->with('success', 'TV berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tv $tv)
    {
        $tv->delete();
        return redirect()->route('tvs.index')->with('success', 'TV berhasil dihapus!');
    }
}
