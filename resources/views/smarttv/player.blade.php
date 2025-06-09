@extends('layouts.tv')

@section('title', 'Smart TV Player - ' . $playlist->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Video Player Area -->
        <div class="col-12">
            <div class="video-container position-relative">
                <video id="mainVideoPlayer"
                       class="video-player w-100"
                       controls
                       autoplay
                       preload="metadata"
                       poster="">
                    <source src="" type="video/mp4">
                    Browser Anda tidak mendukung pemutar video HTML5.
                </video>

                <!-- Video Overlay Info -->
                <div class="position-absolute top-0 start-0 p-3 text-white" style="background: linear-gradient(135deg, rgba(0,0,0,0.8), transparent);">
                    <h4 class="playlist-title">{{ $playlist->name }}</h4>
                    <p class="mb-1" id="currentVideoTitle">Loading...</p>
                    <small class="video-counter" id="videoCounter">0 / {{ $playlist->videos->count() }}</small>
                    <div class="mt-2">
                        <small class="badge bg-success" id="autoRefreshStatus">
                            <i class="bi bi-arrow-clockwise me-1"></i>Auto-refresh: ON
                        </small>
                        <small class="badge bg-primary ms-2" id="autoFullscreenStatus">
                            <i class="bi bi-arrows-fullscreen me-1"></i>Auto-fullscreen: ON
                        </small>
                    </div>
                </div>

                <!-- Start Button Overlay for User Interaction -->
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75" 
                     id="startOverlay" style="z-index: 1000;">
                    <div class="text-center text-white">
                        <div class="mb-4">
                            <i class="bi bi-play-circle display-1 mb-3" style="font-size: 4rem;"></i>
                            <h3 class="mb-2">{{ $playlist->name }}</h3>
                            <p class="text-muted">{{ $playlist->videos->count() }} video{{ $playlist->videos->count() != 1 ? 's' : '' }} ready to play</p>
                        </div>
                        <button class="btn btn-primary btn-lg px-5 py-3" id="startButton" onclick="startPlayback()">
                            <i class="bi bi-play-fill me-2"></i>START PLAYLIST
                        </button>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Click to enable autoplay and fullscreen
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Panel -->
    <div class="control-panel">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="video-info">
                    <h5 class="video-title mb-2" id="videoTitle">Memuat video...</h5>
                    <div class="d-flex justify-content-center align-items-center">
                        <span class="me-3" id="currentTime">00:00</span>
                        <div class="progress flex-grow-1 mx-3" style="height: 8px;">
                            <div class="progress-bar bg-primary"
                                 id="progressBar"
                                 role="progressbar"
                                 style="width: 0%"></div>
                        </div>
                        <span class="ms-3" id="totalTime">00:00</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="d-flex justify-content-center align-items-center">
                    <button class="control-btn" id="prevBtn" title="Video Sebelumnya">
                        <i class="bi bi-skip-backward-fill"></i>
                    </button>
                    <button class="control-btn" id="playPauseBtn" title="Play/Pause">
                        <i class="bi bi-play-fill"></i>
                    </button>
                    <button class="control-btn" id="nextBtn" title="Video Selanjutnya">
                        <i class="bi bi-skip-forward-fill"></i>
                    </button>
                    <button class="control-btn" id="fullscreenBtn" title="Fullscreen">
                        <i class="bi bi-fullscreen"></i>
                    </button>
                    <button class="control-btn" id="backBtn" title="Kembali ke Menu" onclick="goBack()">
                        <i class="bi bi-house-fill"></i>
                    </button>
                    <button class="control-btn" id="refreshBtn" title="Refresh Playlist" onclick="manualRefresh()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Playlist Sidebar (Hidden by default, shown on demand) -->
    <div class="playlist-sidebar position-fixed top-0 end-0 h-100 bg-dark text-white p-3"
         style="width: 300px; transform: translateX(100%); transition: transform 0.3s ease; z-index: 1050;"
         id="playlistSidebar">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Daftar Video</h6>
            <button class="btn btn-sm btn-outline-light" onclick="togglePlaylist()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="playlist-items" id="playlistItems">
            <!-- Items will be populated by JavaScript -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
/* Refresh button animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spinning {
    animation: spin 1s linear infinite;
}

/* Enhanced control button styling */
.control-btn {
    background: rgba(0, 0, 0, 0.7);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 12px;
    margin: 0 5px;
    border-radius: 50%;
    transition: all 0.3s ease;
    font-size: 1.2rem;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.control-btn:hover {
    background: rgba(0, 123, 255, 0.8);
    border-color: #007bff;
    transform: scale(1.1);
}

.control-btn:active {
    transform: scale(0.95);
}

.control-btn.active {
    background: rgba(0, 123, 255, 0.9);
    border-color: #007bff;
}

/* Status badges styling */
.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
}

/* Notification styling improvements */
.alert {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Start Overlay Styling */
#startOverlay {
    backdrop-filter: blur(10px);
    transition: opacity 0.5s ease;
}

#startButton {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    border-radius: 50px;
    font-weight: bold;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

#startButton:hover {
    background: linear-gradient(45deg, #0056b3, #004085);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.6);
}

#startButton:active {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(0, 123, 255, 0.4);
}

#startButton i {
    font-size: 1.2rem;
}
</style>
<script>
// Global variables
let currentPlaylist = @json($playlist);
let currentVideoIndex = 0;
let videoPlayer = null;
let isAutoPlay = {{ $playlist->auto_play ? 'true' : 'false' }};
let isLoop = {{ $playlist->loop_playlist ? 'true' : 'false' }};

// Auto-refresh variables
let autoRefreshInterval = null;
let lastPlaylistUpdate = null;
let refreshIntervalMs = 30000; // 30 seconds
let isRefreshing = false;

// Auto fullscreen variables
let autoFullscreenEnabled = true;
let isAutoFullscreenTriggered = false;
let userHasInteracted = false;

document.addEventListener('DOMContentLoaded', function() {
    videoPlayer = document.getElementById('mainVideoPlayer');

    // Initialize auto fullscreen preference from localStorage
    const savedAutoFullscreen = localStorage.getItem('autoFullscreenEnabled');
    if (savedAutoFullscreen !== null) {
        autoFullscreenEnabled = savedAutoFullscreen === 'true';
    }
    updateAutoFullscreenStatus();

    initializePlayer();
    setupEventListeners();
    
    // Don't auto-load video until user interaction
    // loadVideo(0);
    startAutoRefresh();
});

function initializePlayer() {
    // Setup video player event listeners
    videoPlayer.addEventListener('loadedmetadata', updateVideoInfo);
    videoPlayer.addEventListener('timeupdate', updateProgress);
    videoPlayer.addEventListener('ended', handleVideoEnd);
    videoPlayer.addEventListener('play', updatePlayButton);
    videoPlayer.addEventListener('pause', updatePlayButton);
    videoPlayer.addEventListener('error', handleVideoError);

    // Populate playlist sidebar
    populatePlaylistSidebar();
    
    // Load first video data without playing
    if (currentPlaylist.videos.length > 0) {
        const firstVideo = currentPlaylist.videos[0];
        document.getElementById('currentVideoTitle').textContent = firstVideo.title;
        document.getElementById('videoTitle').textContent = firstVideo.title;
        document.getElementById('videoCounter').textContent = `1 / ${currentPlaylist.videos.length}`;
    }
}

function setupEventListeners() {
    // Control buttons
    document.getElementById('playPauseBtn').addEventListener('click', () => {
        if (!userHasInteracted) {
            startPlayback();
        } else {
            togglePlayPause();
        }
    });
    
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (!userHasInteracted) {
            startPlayback();
            setTimeout(() => playPrevious(), 500);
        } else {
            playPrevious();
        }
    });
    
    document.getElementById('nextBtn').addEventListener('click', () => {
        if (!userHasInteracted) {
            startPlayback();
            setTimeout(() => playNext(), 500);
        } else {
            playNext();
        }
    });
    
    document.getElementById('fullscreenBtn').addEventListener('click', () => {
        if (!userHasInteracted) {
            startPlayback();
        } else {
            toggleFullscreen();
        }
    });

    // Keyboard controls for TV remote
    document.addEventListener('keydown', handleKeyboard);

    // Progress bar click
    document.querySelector('.progress').addEventListener('click', seekVideo);
}

function loadVideo(index) {
    if (index < 0 || index >= currentPlaylist.videos.length) return;

    currentVideoIndex = index;
    const video = currentPlaylist.videos[index];

    // Update video source
    videoPlayer.src = video.video_url;
    videoPlayer.poster = video.thumbnail_url || '';

    // Update UI
    document.getElementById('currentVideoTitle').textContent = video.title;
    document.getElementById('videoTitle').textContent = video.title;
    document.getElementById('videoCounter').textContent = `${index + 1} / ${currentPlaylist.videos.length}`;

    // Update playlist sidebar
    updatePlaylistHighlight();

    // Only auto play if user has interacted and autoplay is enabled
    if (isAutoPlay && userHasInteracted) {
        videoPlayer.play().catch(e => console.log('Auto-play prevented:', e));
    }

    // Trigger auto fullscreen for the first video only if user has interacted
    if (autoFullscreenEnabled && !isAutoFullscreenTriggered && index === 0 && userHasInteracted) {
        triggerAutoFullscreen();
    }
}

// Function to handle START button click
function startPlayback() {
    userHasInteracted = true;
    
    // Hide the start overlay
    const startOverlay = document.getElementById('startOverlay');
    startOverlay.style.opacity = '0';
    
    setTimeout(() => {
        startOverlay.style.display = 'none';
    }, 500);

    // Load and start the first video
    loadVideo(0);
    
    // Attempt to enter fullscreen if auto-fullscreen is enabled
    if (autoFullscreenEnabled) {
        triggerAutoFullscreen();
    }
    
    // Start playing the video
    if (isAutoPlay) {
        videoPlayer.play().then(() => {
            console.log('Playback started successfully');
            showRefreshNotification('Playlist started!', 'success');
        }).catch(error => {
            console.error('Playback failed:', error);
            showRefreshNotification('Failed to start playback', 'error');
        });
    }
}

function updateVideoInfo() {
    const duration = videoPlayer.duration;
    document.getElementById('totalTime').textContent = formatTime(duration);
}

function updateProgress() {
    const progress = (videoPlayer.currentTime / videoPlayer.duration) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('currentTime').textContent = formatTime(videoPlayer.currentTime);
}

function handleVideoEnd() {
    if (currentVideoIndex < currentPlaylist.videos.length - 1) {
        // Play next video
        playNext();
    } else if (isLoop) {
        // Loop back to first video
        loadVideo(0);
    } else {
        // End of playlist
        showEndMessage();
    }
}

function togglePlayPause() {
    if (videoPlayer.paused) {
        videoPlayer.play();
    } else {
        videoPlayer.pause();
    }
}

function updatePlayButton() {
    const playBtn = document.getElementById('playPauseBtn');
    const icon = playBtn.querySelector('i');

    if (videoPlayer.paused) {
        icon.className = 'bi bi-play-fill';
        playBtn.classList.remove('active');
    } else {
        icon.className = 'bi bi-pause-fill';
        playBtn.classList.add('active');
    }
}

function playNext() {
    const nextIndex = currentVideoIndex + 1;
    if (nextIndex < currentPlaylist.videos.length) {
        loadVideo(nextIndex);
    } else if (isLoop) {
        loadVideo(0);
    }
}

function playPrevious() {
    const prevIndex = currentVideoIndex - 1;
    if (prevIndex >= 0) {
        loadVideo(prevIndex);
    } else if (isLoop) {
        loadVideo(currentPlaylist.videos.length - 1);
    }
}

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        videoPlayer.requestFullscreen().catch(err => {
            console.log('Fullscreen failed:', err);
        });
    } else {
        document.exitFullscreen();
    }
}

function seekVideo(e) {
    const progressBar = e.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const pos = (e.clientX - rect.left) / rect.width;
    videoPlayer.currentTime = pos * videoPlayer.duration;
}

function handleKeyboard(e) {
    // Check if start overlay is visible
    const startOverlay = document.getElementById('startOverlay');
    if (startOverlay.style.display !== 'none' && window.getComputedStyle(startOverlay).opacity !== '0') {
        // If start overlay is visible, any key should start playback
        if (e.key === ' ' || e.key === 'Enter') {
            e.preventDefault();
            startPlayback();
            return;
        }
    }

    // Normal keyboard controls (only work after user interaction)
    if (!userHasInteracted) return;

    switch(e.key) {
        case ' ':
        case 'Enter':
            e.preventDefault();
            togglePlayPause();
            break;
        case 'ArrowLeft':
            e.preventDefault();
            videoPlayer.currentTime = Math.max(0, videoPlayer.currentTime - 10);
            break;
        case 'ArrowRight':
            e.preventDefault();
            videoPlayer.currentTime = Math.min(videoPlayer.duration, videoPlayer.currentTime + 10);
            break;
        case 'ArrowUp':
            e.preventDefault();
            playPrevious();
            break;
        case 'ArrowDown':
            e.preventDefault();
            playNext();
            break;
        case 'f':
        case 'F':
            e.preventDefault();
            toggleFullscreen();
            break;
        case 'Escape':
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                goBack();
            }
            break;
        case 'p':
        case 'P':
            e.preventDefault();
            togglePlaylist();
            break;
        case 'r':
        case 'R':
            e.preventDefault();
            manualRefresh();
            break;
        case 'a':
        case 'A':
            e.preventDefault();
            toggleAutoFullscreen();
            break;
    }
}

function populatePlaylistSidebar() {
    const container = document.getElementById('playlistItems');
    container.innerHTML = '';

    currentPlaylist.videos.forEach((video, index) => {
        const item = document.createElement('div');
        item.className = 'playlist-item p-2 mb-2 rounded cursor-pointer';
        item.style.cssText = 'cursor: pointer; transition: background-color 0.2s;';
        item.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="me-3 text-center" style="min-width: 30px;">
                    <span class="badge bg-secondary">${index + 1}</span>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">${video.title}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">${formatTime(video.duration || 0)}</div>
                </div>
            </div>
        `;

        item.addEventListener('click', () => {
            // If user hasn't interacted, start playback first
            if (!userHasInteracted) {
                startPlayback();
                setTimeout(() => loadVideo(index), 500);
            } else {
                loadVideo(index);
            }
        });
        
        item.addEventListener('mouseenter', () => item.style.backgroundColor = 'rgba(255,255,255,0.1)');
        item.addEventListener('mouseleave', () => item.style.backgroundColor = 'transparent');

        container.appendChild(item);
    });
}

function updatePlaylistHighlight() {
    const items = document.querySelectorAll('.playlist-item');
    items.forEach((item, index) => {
        if (index === currentVideoIndex) {
            item.style.backgroundColor = 'rgba(0,123,255,0.3)';
            item.style.borderLeft = '4px solid #007bff';
        } else {
            item.style.backgroundColor = 'transparent';
            item.style.borderLeft = 'none';
        }
    });
}

function togglePlaylist() {
    const sidebar = document.getElementById('playlistSidebar');
    const isVisible = sidebar.style.transform === 'translateX(0px)';

    sidebar.style.transform = isVisible ? 'translateX(100%)' : 'translateX(0px)';
}

function showEndMessage() {
    alert('Playlist selesai diputar!');
}

function goBack() {
    if (confirm('Kembali ke menu utama?')) {
        window.location.href = '{{ route("tv.index") }}';
    }
}

function handleVideoError(e) {
    console.error('Video error:', e);
    const errorMsg = 'Error memuat video. Mencoba video selanjutnya...';
    document.getElementById('videoTitle').textContent = errorMsg;

    // Auto skip to next video after 3 seconds
    setTimeout(() => {
        playNext();
    }, 3000);
}

function formatTime(seconds) {
    if (!seconds || isNaN(seconds)) return '00:00';

    const hrs = Math.floor(seconds / 3600);
    const mins = Math.floor((seconds % 3600) / 60);
    const secs = Math.floor(seconds % 60);

    if (hrs > 0) {
        return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

// Auto-refresh functions
function startAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    // Store initial playlist state
    lastPlaylistUpdate = Date.now();
    
    autoRefreshInterval = setInterval(() => {
        if (!isRefreshing) {
            checkForPlaylistUpdates();
        }
    }, refreshIntervalMs);
    
    updateAutoRefreshStatus(true);
    console.log('Auto-refresh started with interval:', refreshIntervalMs + 'ms');
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
    updateAutoRefreshStatus(false);
    console.log('Auto-refresh stopped');
}

function checkForPlaylistUpdates() {
    if (isRefreshing) return;
    
    isRefreshing = true;
    
    fetch(`/tv/api/playlist/${currentPlaylist.id}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Received playlist data:', data);
        
        // Validate the response structure
        if (!data || !Array.isArray(data.videos)) {
            throw new Error('Invalid playlist data received');
        }
        
        if (hasPlaylistChanged(data)) {
            updatePlaylistData(data);
            showRefreshNotification('Playlist updated!');
        } else {
            console.log('No changes detected in playlist');
        }
        lastPlaylistUpdate = Date.now();
    })
    .catch(error => {
        console.error('Error checking for playlist updates:', error);
        showRefreshNotification('Failed to check for updates', 'error');
    })
    .finally(() => {
        isRefreshing = false;
    });
}

function hasPlaylistChanged(newPlaylist) {
    // Validate input
    if (!newPlaylist || !Array.isArray(newPlaylist.videos)) {
        console.error('Invalid playlist data for comparison');
        return false;
    }
    
    if (!currentPlaylist || !Array.isArray(currentPlaylist.videos)) {
        console.error('Current playlist data is invalid');
        return false;
    }
    
    // Check if video count changed
    if (newPlaylist.videos.length !== currentPlaylist.videos.length) {
        console.log('Video count changed:', currentPlaylist.videos.length, '->', newPlaylist.videos.length);
        return true;
    }
    
    // Check if video order or content changed
    for (let i = 0; i < newPlaylist.videos.length; i++) {
        const currentVideo = currentPlaylist.videos[i];
        const newVideo = newPlaylist.videos[i];
        
        if (!currentVideo || !newVideo) {
            console.log('Missing video data at index', i);
            return true;
        }
        
        if (currentVideo.id !== newVideo.id || 
            currentVideo.title !== newVideo.title ||
            currentVideo.video_url !== newVideo.video_url) {
            console.log('Video content changed at index', i);
            return true;
        }
    }
    
    // Check if playlist settings changed
    if (newPlaylist.auto_play !== currentPlaylist.auto_play ||
        newPlaylist.loop_playlist !== currentPlaylist.loop_playlist) {
        console.log('Playlist settings changed');
        return true;
    }
    
    return false;
}

function updatePlaylistData(newPlaylist) {
    const currentVideoId = currentPlaylist.videos[currentVideoIndex]?.id;
    
    // Update playlist data
    currentPlaylist = newPlaylist;
    isAutoPlay = newPlaylist.auto_play;
    isLoop = newPlaylist.loop_playlist;
    
    // Try to maintain current video position
    let newVideoIndex = currentVideoIndex; // Keep current index as fallback
    if (currentVideoId) {
        const videoIndex = newPlaylist.videos.findIndex(video => video.id === currentVideoId);
        if (videoIndex !== -1) {
            newVideoIndex = videoIndex;
            currentVideoIndex = newVideoIndex; // Update the current index
        }
    }
    
    // Update UI
    document.getElementById('videoCounter').textContent = `${newVideoIndex + 1} / ${newPlaylist.videos.length}`;
    populatePlaylistSidebar();
    updatePlaylistHighlight();
    
    console.log('Playlist data updated. Videos:', newPlaylist.videos.length);
}

function manualRefresh() {
    const refreshBtn = document.getElementById('refreshBtn');
    const icon = refreshBtn.querySelector('i');
    
    // Add spinning animation
    icon.classList.add('spinning');
    refreshBtn.disabled = true;
    
    checkForPlaylistUpdates();
    
    // Remove animation after 2 seconds
    setTimeout(() => {
        icon.classList.remove('spinning');
        refreshBtn.disabled = false;
    }, 2000);
}

function updateAutoRefreshStatus(isActive = null) {
    const statusElement = document.getElementById('autoRefreshStatus');
    const isCurrentlyActive = autoRefreshInterval !== null;
    const status = isActive !== null ? isActive : isCurrentlyActive;
    
    if (status) {
        statusElement.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Auto-refresh: ON';
        statusElement.className = 'badge bg-success';
    } else {
        statusElement.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Auto-refresh: OFF';
        statusElement.className = 'badge bg-secondary';
    }
}

function showRefreshNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    notification.innerHTML = `
        <i class="bi bi-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Fade in
    setTimeout(() => notification.style.opacity = '1', 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Auto fullscreen functions
function triggerAutoFullscreen() {
    if (autoFullscreenEnabled && !document.fullscreenElement && userHasInteracted) {
        // Try to request fullscreen with better error handling
        if (videoPlayer.requestFullscreen) {
            videoPlayer.requestFullscreen().then(() => {
                console.log('Auto fullscreen activated successfully');
                isAutoFullscreenTriggered = true;
                showRefreshNotification('Entered fullscreen mode', 'success');
            }).catch(err => {
                console.log('Auto fullscreen failed:', err.message);
                // Don't show error notification for permission denied, as it's expected behavior
                if (err.name !== 'NotAllowedError') {
                    showRefreshNotification('Fullscreen not available', 'error');
                }
            });
        } else if (videoPlayer.webkitRequestFullscreen) {
            // Safari fallback
            videoPlayer.webkitRequestFullscreen();
            isAutoFullscreenTriggered = true;
        } else if (videoPlayer.msRequestFullscreen) {
            // IE/Edge fallback
            videoPlayer.msRequestFullscreen();
            isAutoFullscreenTriggered = true;
        } else {
            console.log('Fullscreen API not supported');
        }
    }
}

function toggleAutoFullscreen() {
    autoFullscreenEnabled = !autoFullscreenEnabled;
    localStorage.setItem('autoFullscreenEnabled', autoFullscreenEnabled.toString());
    updateAutoFullscreenStatus();
    showRefreshNotification(
        `Auto-fullscreen ${autoFullscreenEnabled ? 'enabled' : 'disabled'}`,
        'success'
    );
}

function updateAutoFullscreenStatus() {
    const statusElement = document.getElementById('autoFullscreenStatus');
    
    if (autoFullscreenEnabled) {
        statusElement.innerHTML = '<i class="bi bi-arrows-fullscreen me-1"></i>Auto-fullscreen: ON';
        statusElement.className = 'badge bg-primary';
    } else {
        statusElement.innerHTML = '<i class="bi bi-arrows-fullscreen me-1"></i>Auto-fullscreen: OFF';
        statusElement.className = 'badge bg-secondary';
    }
}

// Auto-hide cursor in fullscreen mode
let cursorTimeout;
document.addEventListener('mousemove', () => {
    document.body.style.cursor = 'default';
    clearTimeout(cursorTimeout);

    if (document.fullscreenElement) {
        cursorTimeout = setTimeout(() => {
            document.body.style.cursor = 'none';
        }, 3000);
    }
});
</script>
@endpush
