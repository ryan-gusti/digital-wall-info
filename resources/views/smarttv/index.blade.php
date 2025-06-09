@extends('layouts.tv')

@section('title', 'Smart TV - Pilih Playlist')

@section('content')
<div class="container">
    <!-- Loading overlay -->
    <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.8); z-index: 9999;">
        <div class="text-center text-white">
            <div class="spinner-border spinner-border-lg mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4>Mendeteksi TV...</h4>
            <p>Sedang mengecek konfigurasi untuk TV ini</p>
        </div>
    </div>

    <div class="text-center mb-5">
        <h1 class="display-4 text-white fw-bold">
            <i class="bi bi-tv me-3"></i>
            Digital Wall Info
        </h1>
        <p class="lead text-white-50">Pilih playlist video untuk ditampilkan</p>

        <!-- Alert containers for different statuses -->
        <div id="alert-container" class="mt-3" style="display: none;">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>

    @if($playlists->count() > 0)
        <div class="row">
            @foreach($playlists as $playlist)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="playlist-card card h-100"
                         onclick="playPlaylist({{ $playlist->id }})"
                         style="cursor: pointer;">

                        <div class="playlist-thumbnail d-flex align-items-center justify-content-center">
                            <i class="bi bi-collection-play text-primary" style="font-size: 4rem;"></i>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $playlist->name }}</h5>

                            @if($playlist->description)
                                <p class="card-text text-muted small">
                                    {{ Str::limit($playlist->description, 100) }}
                                </p>
                            @endif

                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="text-primary fw-bold">{{ $playlist->videos->count() }}</div>
                                    <small class="text-muted">Videos</small>
                                </div>
                                <div class="col-4">
                                    <div class="text-success fw-bold">{{ $playlist->formatted_total_duration ?? '00:00' }}</div>
                                    <small class="text-muted">Durasi</small>
                                </div>
                                <div class="col-4">
                                    @if($playlist->loop_playlist)
                                        <div class="text-warning fw-bold"><i class="bi bi-arrow-repeat"></i></div>
                                        <small class="text-muted">Loop</small>
                                    @else
                                        <div class="text-info fw-bold"><i class="bi bi-play-fill"></i></div>
                                        <small class="text-muted">Play</small>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">{{ $playlist->auto_play ? 'Auto Play' : 'Manual' }}</span>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $playlist->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-collection-play text-white-50" style="font-size: 6rem;"></i>
            <h3 class="text-white mt-4">Tidak ada playlist aktif</h3>
            <p class="text-white-50">Silakan hubungi administrator untuk mengaktifkan playlist.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Global variables
let detectedIP = null;
let tvData = null;

// Function to hide loading overlay
function hideLoading() {
    document.getElementById('loading-overlay').style.display = 'none';
}

// Function to show alert
function showAlert(type, title, message, ipAddress = null) {
    const alertContainer = document.getElementById('alert-container');
    let alertClass = 'alert-warning';
    let icon = 'bi-exclamation-triangle';

    switch(type) {
        case 'not_registered':
            alertClass = 'alert-warning';
            icon = 'bi-exclamation-triangle';
            break;
        case 'no_playlist':
            alertClass = 'alert-info';
            icon = 'bi-info-circle';
            break;
        case 'error':
            alertClass = 'alert-danger';
            icon = 'bi-x-circle';
            break;
    }

    let ipInfo = ipAddress ? `<br>IP Address: <code>${ipAddress}</code>` : '';

    alertContainer.innerHTML = `
        <div class="alert ${alertClass}">
            <i class="bi ${icon} me-2"></i>
            <strong>${title}</strong><br>
            ${message}${ipInfo}
        </div>
    `;
    alertContainer.style.display = 'block';
}

// Function to get external IP address
async function getExternalIP() {
    try {
        console.log('Fetching external IP from API...');
        const response = await fetch('http://107.102.8.148/ip.php', {
            method: 'GET',
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('External IP API response:', data);

        if (data.ip_address) {
            detectedIP = data.ip_address;
            return data.ip_address;
        } else {
            throw new Error('IP address not found in response');
        }
    } catch (error) {
        console.error('Error fetching external IP:', error);

        // Fallback: try to get IP from another service
        try {
            console.log('Trying fallback IP service...');
            const fallbackResponse = await fetch('https://api.ipify.org?format=json');
            const fallbackData = await fallbackResponse.json();
            detectedIP = fallbackData.ip;
            return fallbackData.ip;
        } catch (fallbackError) {
            console.error('Fallback IP service also failed:', fallbackError);
            throw new Error('Unable to detect IP address');
        }
    }
}

// Function to check TV registration
async function checkTVRegistration(ipAddress) {
    try {
        console.log('Checking TV registration for IP:', ipAddress);

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch('{{ route("tv.check-ip") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                ip_address: ipAddress
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('TV registration check response:', data);

        return data;
    } catch (error) {
        console.error('Error checking TV registration:', error);
        throw error;
    }
}

// Function to handle TV registration status
function handleTVStatus(statusData) {
    hideLoading();

    switch(statusData.status) {
        case 'redirect':
            console.log('TV registered with playlist, redirecting...');
            // Show success message briefly before redirecting
            showAlert('info', 'TV Terdaftar!',
                `TV ${statusData.tv.name} (${statusData.tv.location}) akan memutar playlist: ${statusData.tv.playlist_name}`);

            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = statusData.redirect_url;
            }, 2000);
            break;

        case 'no_playlist':
            showAlert('no_playlist', 'Playlist belum diatur!',
                `TV ${statusData.tv.name} (${statusData.tv.location}) sudah terdaftar tetapi belum memiliki playlist yang ditentukan. Silakan atur playlist untuk TV ini di menu TV Management.`);
            break;

        case 'not_registered':
            showAlert('not_registered', 'TV belum terdaftar!',
                'TV ini belum terdaftar dalam sistem. Silakan daftarkan TV ini terlebih dahulu di menu TV Management.',
                statusData.ip_address);
            break;

        case 'manual_selection':
        default:
            // Just hide loading, let user select manually
            break;
    }
}

// Main initialization function
async function initializeTVCheck() {
    try {
        // Step 1: Get external IP
        const ipAddress = await getExternalIP();
        console.log('Detected IP:', ipAddress);

        // Step 2: Check TV registration
        const statusData = await checkTVRegistration(ipAddress);

        // Step 3: Handle the response
        handleTVStatus(statusData);

    } catch (error) {
        console.error('Initialization error:', error);
        hideLoading();
        showAlert('error', 'Error!',
            'Terjadi kesalahan saat mendeteksi konfigurasi TV. Silakan pilih playlist secara manual atau hubungi administrator.');
    }
}

// Existing playlist functions
function playPlaylist(playlistId) {
    // Animasi loading
    document.body.style.cursor = 'wait';

    // Redirect ke halaman player
    window.location.href = `{{ route('tv.play', '') }}/${playlistId}`;
}

// Auto refresh setiap 5 menit untuk update playlist
setInterval(function() {
    window.location.reload();
}, 300000);

// Keyboard navigation untuk TV remote
document.addEventListener('keydown', function(e) {
    // Don't handle keyboard events during loading
    if (document.getElementById('loading-overlay').style.display !== 'none') {
        return;
    }

    const playlistCards = document.querySelectorAll('.playlist-card');
    const activeCard = document.querySelector('.playlist-card.active') || playlistCards[0];

    if (!activeCard) return;

    // Remove active class from all cards
    playlistCards.forEach(card => card.classList.remove('active', 'border-primary'));

    let nextCard = activeCard;

    switch(e.key) {
        case 'ArrowUp':
            e.preventDefault();
            // Logic untuk navigasi ke atas
            break;
        case 'ArrowDown':
            e.preventDefault();
            // Logic untuk navigasi ke bawah
            break;
        case 'ArrowLeft':
            e.preventDefault();
            const prevIndex = Array.from(playlistCards).indexOf(activeCard) - 1;
            if (prevIndex >= 0) nextCard = playlistCards[prevIndex];
            break;
        case 'ArrowRight':
            e.preventDefault();
            const nextIndex = Array.from(playlistCards).indexOf(activeCard) + 1;
            if (nextIndex < playlistCards.length) nextCard = playlistCards[nextIndex];
            break;
        case 'Enter':
            e.preventDefault();
            activeCard.click();
            return;
        case 'Escape':
            e.preventDefault();
            // Reload page to restart detection
            window.location.reload();
            return;
    }

    // Add active class to selected card
    nextCard.classList.add('active', 'border-primary');
    nextCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
});

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, starting TV initialization...');

    // Start the IP detection and TV check process
    initializeTVCheck();

    // Initialize first card as active for manual selection
    setTimeout(() => {
        const firstCard = document.querySelector('.playlist-card');
        if (firstCard && document.getElementById('loading-overlay').style.display === 'none') {
            firstCard.classList.add('active', 'border-primary');
        }
    }, 100);
});
</script>
@endpush
