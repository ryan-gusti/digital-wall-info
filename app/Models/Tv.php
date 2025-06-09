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
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
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
}
