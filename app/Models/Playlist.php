<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'auto_play',
        'loop_playlist',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_play' => 'boolean',
        'loop_playlist' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationship dengan videos melalui pivot table
    public function videos()
    {
        return $this->belongsToMany(Video::class, 'playlist_videos')
                    ->withPivot('sort_order')
                    ->withTimestamps()
                    ->orderBy('playlist_videos.sort_order');
    }

    // Scope untuk playlist aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get total durasi playlist
    public function getTotalDurationAttribute()
    {
        return $this->videos->sum('duration');
    }

    // Get formatted total durasi
    public function getFormattedTotalDurationAttribute()
    {
        $totalSeconds = $this->total_duration;
        if (!$totalSeconds) return '00:00';

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
