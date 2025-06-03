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

    // Accessor untuk format durasi
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return null;

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
