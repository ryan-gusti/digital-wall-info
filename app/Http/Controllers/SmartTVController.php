<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use App\Models\Tv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SmartTVController extends Controller
{
    /**
     * Display TV interface with available playlists or auto-redirect based on IP
     */
    public function index(Request $request)
    {
        // Tampilkan halaman dengan JavaScript untuk pengecekan IP eksternal
        $playlists = Playlist::active()
            ->with('videos')
            ->orderBy('sort_order')
            ->get();

        return view('smarttv.index', compact('playlists'));
    }

    /**
     * Check TV registration based on IP address from external API
     */
    public function checkTvByIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip'
        ]);

        $clientIp = $request->input('ip_address');

        // Cek apakah TV dengan IP ini sudah terdaftar
        $tv = Tv::active()->where('ip_address', $clientIp)->with('playlist')->first();

        // Update last_seen timestamp if TV is found
        if ($tv) {
            $tv->updateLastSeen();
        }

        // Jika TV ditemukan dan memiliki playlist, return redirect URL
        if ($tv && $tv->playlist && $tv->playlist->is_active) {
            return response()->json([
                'status' => 'redirect',
                'redirect_url' => route('tv.play', $tv->playlist),
                'tv' => [
                    'name' => $tv->name,
                    'location' => $tv->location,
                    'playlist_name' => $tv->playlist->name
                ]
            ]);
        }

        // Jika TV ditemukan tapi tidak ada playlist yang diatur
        if ($tv && !$tv->playlist) {
            return response()->json([
                'status' => 'no_playlist',
                'message' => 'TV terdaftar tetapi belum memiliki playlist yang ditentukan',
                'tv' => [
                    'name' => $tv->name,
                    'location' => $tv->location
                ]
            ]);
        }

        // Jika TV tidak terdaftar
        if (!$tv) {
            return response()->json([
                'status' => 'not_registered',
                'message' => 'TV belum terdaftar dalam sistem',
                'ip_address' => $clientIp
            ]);
        }

        // Default response
        return response()->json([
            'status' => 'manual_selection',
            'message' => 'Silakan pilih playlist secara manual'
        ]);
    }

    /**
     * Update TV last seen timestamp (called during auto-refresh)
     */
    public function updateLastSeen(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip'
        ]);

        $clientIp = $request->input('ip_address');
        $tv = Tv::where('ip_address', $clientIp)->first();

        if ($tv) {
            $tv->updateLastSeen();
            return response()->json(['status' => 'success', 'last_seen' => $tv->last_seen]);
        }

        return response()->json(['status' => 'not_found'], 404);
    }

    /**
     * Play a specific playlist
     */
    public function playPlaylist(Playlist $playlist)
    {
        if (!$playlist->is_active) {
            abort(404, 'Playlist tidak aktif');
        }

        $playlist->load(['videos' => function ($query) {
            $query->orderBy('pivot_sort_order');
        }]);

        // Transform the playlist data to ensure correct video URLs
        $playlist->videos->transform(function ($video) {
            $video->video_url = $video->videoUrl(); // Use the method that handles both local and external URLs
            return $video;
        });

        return view('smarttv.player', compact('playlist'));
    }

    /**
     * Get playlist data as JSON for AJAX requests
     */
    public function getPlaylistData(Playlist $playlist)
    {
        if (!$playlist->is_active) {
            return response()->json(['error' => 'Playlist tidak aktif'], 404);
        }

        $playlist->load('videos');

        return response()->json([
            'id' => $playlist->id,
            'name' => $playlist->name,
            'description' => $playlist->description,
            'auto_play' => $playlist->auto_play,
            'loop_playlist' => $playlist->loop_playlist,
            'videos' => $playlist->videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'video_url' => $video->videoUrl(), // Use the method that handles both local and external URLs
                    'thumbnail_url' => $video->thumbnail_url,
                    'duration' => $video->duration,
                    'formatted_duration' => $video->formatted_duration,
                    'sort_order' => $video->pivot->sort_order
                ];
            })
        ]);
    }

    /**
     * Get all active playlists as JSON
     */
    public function getActivePlaylists()
    {
        $playlists = Playlist::active()
            ->with('videos')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'description' => $playlist->description,
                    'video_count' => $playlist->videos->count(),
                    'total_duration' => $playlist->formatted_total_duration,
                    'auto_play' => $playlist->auto_play,
                    'loop_playlist' => $playlist->loop_playlist
                ];
            });

        return response()->json($playlists);
    }

    /**
     * Remote control endpoint for TV navigation
     */
    public function remoteControl(Request $request)
    {
        $action = $request->input('action');
        $playlistId = $request->input('playlist_id');
        $videoIndex = $request->input('video_index', 0);

        switch ($action) {
            case 'play':
                return $this->handlePlay($playlistId, $videoIndex);
            case 'pause':
                return response()->json(['status' => 'paused']);
            case 'next':
                return $this->handleNext($playlistId, $videoIndex);
            case 'previous':
                return $this->handlePrevious($playlistId, $videoIndex);
            case 'stop':
                return response()->json(['status' => 'stopped']);
            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }

    private function handlePlay($playlistId, $videoIndex)
    {
        $playlist = Playlist::active()->with('videos')->find($playlistId);

        if (!$playlist) {
            return response()->json(['error' => 'Playlist not found'], 404);
        }

        $video = $playlist->videos->get($videoIndex);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        return response()->json([
            'status' => 'playing',
            'video' => [
                'id' => $video->id,
                'title' => $video->title,
                'video_url' => $video->video_url,
                'duration' => $video->duration
            ]
        ]);
    }

    private function handleNext($playlistId, $currentIndex)
    {
        $playlist = Playlist::active()->with('videos')->find($playlistId);

        if (!$playlist) {
            return response()->json(['error' => 'Playlist not found'], 404);
        }

        $nextIndex = $currentIndex + 1;
        $totalVideos = $playlist->videos->count();

        if ($nextIndex >= $totalVideos) {
            if ($playlist->loop_playlist) {
                $nextIndex = 0;
            } else {
                return response()->json(['status' => 'end_of_playlist']);
            }
        }

        return response()->json([
            'status' => 'next',
            'video_index' => $nextIndex,
            'video' => $playlist->videos->get($nextIndex)
        ]);
    }

    private function handlePrevious($playlistId, $currentIndex)
    {
        $playlist = Playlist::active()->with('videos')->find($playlistId);

        if (!$playlist) {
            return response()->json(['error' => 'Playlist not found'], 404);
        }

        $prevIndex = $currentIndex - 1;

        if ($prevIndex < 0) {
            if ($playlist->loop_playlist) {
                $prevIndex = $playlist->videos->count() - 1;
            } else {
                return response()->json(['status' => 'beginning_of_playlist']);
            }
        }

        return response()->json([
            'status' => 'previous',
            'video_index' => $prevIndex,
            'video' => $playlist->videos->get($prevIndex)
        ]);
    }
}
