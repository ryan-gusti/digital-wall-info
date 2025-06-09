<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'file_path',
        'storage_disk',
        'thumbnail_url',
        'duration',
        'video_type',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration' => 'integer',
        'sort_order' => 'integer'
    ];

    // Relationship dengan playlists melalui pivot table
    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_videos')
                    ->withPivot('sort_order')
                    ->withTimestamps();
    }

    // Scope untuk video aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Check if video is stored locally on Z: drive
    public function isStoredLocally()
    {
        return !empty($this->file_path) && !empty($this->storage_disk);
    }

    // Get the appropriate video URL (local file URL or external URL)
    public function videoUrl()
    {
        if ($this->isStoredLocally()) {
            return route('videos.serve-file', ['filename' => $this->file_path]);
        }

        return $this->video_url;
    }

    // Accessor untuk video URL attribute
    public function getVideoUrlAttribute($value)
    {
        // If accessing video_url and it's stored locally, return the serve route
        if ($this->isStoredLocally()) {
            return route('videos.serve-file', ['filename' => $this->file_path]);
        }

        return $value;
    }

    // Accessor untuk format durasi
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return null;

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
