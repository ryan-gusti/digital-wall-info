<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SmartTVController;
use App\Http\Controllers\TvController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Dashboard utama - redirect ke playlists
Route::get('/', function () {
    return redirect()->route('playlists.index');
});

// Resource routes untuk Video Management
Route::resource('videos', VideoController::class);

// Route for serving video files from Z: drive
Route::get('/videos/file/{filename}', [VideoController::class, 'serveFile'])
    ->where('filename', '.*')
    ->name('videos.serve-file');

// Resource routes untuk Playlist Management
Route::resource('playlists', PlaylistController::class);

// Additional playlist routes
Route::post('playlists/{playlist}/add-video', [PlaylistController::class, 'addVideo'])
    ->name('playlists.add-video');
Route::delete('playlists/{playlist}/videos/{video}', [PlaylistController::class, 'removeVideo'])
    ->name('playlists.remove-video');

// Resource routes untuk TV Management
Route::resource('tvs', TvController::class);

// TV Monitoring route
Route::get('tvs-monitoring', [TvController::class, 'monitoring'])->name('tvs.monitoring');

// Smart TV Interface Routes
Route::prefix('tv')->name('tv.')->group(function () {
    Route::get('/', [SmartTVController::class, 'index'])->name('index');
    Route::get('/play/{playlist}', [SmartTVController::class, 'playPlaylist'])->name('play');

    // TV IP checking endpoint
    Route::post('/check-ip', [SmartTVController::class, 'checkTvByIp'])->name('check-ip');

    // TV monitoring endpoint
    Route::post('/update-last-seen', [SmartTVController::class, 'updateLastSeen'])->name('update-last-seen');

    // API endpoints for TV remote control
    Route::get('/api/playlists', [SmartTVController::class, 'getActivePlaylists'])->name('api.playlists');
    Route::get('/api/playlist/{playlist}', [SmartTVController::class, 'getPlaylistData'])->name('api.playlist');
    Route::post('/api/remote', [SmartTVController::class, 'remoteControl'])->name('api.remote');
});
