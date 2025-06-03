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
<script>
// Global variables
let currentPlaylist = @json($playlist);
let currentVideoIndex = 0;
let videoPlayer = null;
let isAutoPlay = {{ $playlist->auto_play ? 'true' : 'false' }};
let isLoop = {{ $playlist->loop_playlist ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    videoPlayer = document.getElementById('mainVideoPlayer');
    initializePlayer();
    setupEventListeners();
    loadVideo(0);
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
}

function setupEventListeners() {
    // Control buttons
    document.getElementById('playPauseBtn').addEventListener('click', togglePlayPause);
    document.getElementById('prevBtn').addEventListener('click', playPrevious);
    document.getElementById('nextBtn').addEventListener('click', playNext);
    document.getElementById('fullscreenBtn').addEventListener('click', toggleFullscreen);

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

    // Auto play if enabled
    if (isAutoPlay) {
        videoPlayer.play().catch(e => console.log('Auto-play prevented:', e));
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
            goBack();
            break;
        case 'p':
        case 'P':
            e.preventDefault();
            togglePlaylist();
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

        item.addEventListener('click', () => loadVideo(index));
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
