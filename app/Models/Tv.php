<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tv extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'location',
        'description',
        'playlist_id',
        'is_active',
        'last_seen'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen' => 'datetime'
    ];

    /**
     * Relationship dengan Playlist
     */
    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    /**
     * Scope untuk TV aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Mendapatkan TV berdasarkan IP address
     */
    public static function findByIpAddress($ipAddress)
    {
        return static::where('ip_address', $ipAddress)->first();
    }

    /**
     * Update last seen timestamp for this TV
     */
    public function updateLastSeen()
    {
        $this->update(['last_seen' => now()]);
    }

    /**
     * Check if TV is considered online (last seen within threshold)
     */
    public function isOnline($thresholdMinutes = 10)
    {
        if (!$this->last_seen) {
            return false;
        }

        return $this->last_seen->diffInMinutes(now()) <= $thresholdMinutes;
    }

    /**
     * Scope for online TVs
     */
    public function scopeOnline($query, $thresholdMinutes = 10)
    {
        return $query->where('last_seen', '>=', now()->subMinutes($thresholdMinutes));
    }

    /**
     * Scope for offline TVs
     */
    public function scopeOffline($query, $thresholdMinutes = 10)
    {
        return $query->where(function($q) use ($thresholdMinutes) {
            $q->whereNull('last_seen')
              ->orWhere('last_seen', '<', now()->subMinutes($thresholdMinutes));
        });
    }

    /**
     * Get formatted last seen time
     */
    public function getLastSeenFormatted()
    {
        if (!$this->last_seen) {
            return 'Never';
        }

        return $this->last_seen->diffForHumans();
    }
}
