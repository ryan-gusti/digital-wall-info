@extends('layouts.tv')

@section('title', 'Smart TV - Pilih Playlist')

@section('content')
<div class="container">
    <div class="text-center mb-5">
        <h1 class="display-4 text-white fw-bold">
            <i class="bi bi-tv me-3"></i>
            Digital Wall Info
        </h1>
        <p class="lead text-white-50">Pilih playlist video untuk ditampilkan</p>
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
    }

    // Add active class to selected card
    nextCard.classList.add('active', 'border-primary');
    nextCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
});

// Initialize first card as active
document.addEventListener('DOMContentLoaded', function() {
    const firstCard = document.querySelector('.playlist-card');
    if (firstCard) {
        firstCard.classList.add('active', 'border-primary');
    }
});
</script>
@endpush
