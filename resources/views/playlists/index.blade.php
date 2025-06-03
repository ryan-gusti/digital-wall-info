@extends('layouts.app')

@section('title', 'Playlist Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-collection-play me-2"></i>
        Playlist Management
    </h1>
    <a href="{{ route('playlists.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Tambah Playlist
    </a>
</div>

@if($playlists->count() > 0)
    <div class="row">
        @foreach($playlists as $playlist)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">{{ $playlist->name }}</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('playlists.show', $playlist) }}">
                                            <i class="bi bi-eye me-2"></i>Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('playlists.edit', $playlist) }}">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('tv.play', $playlist) }}" target="_blank">
                                            <i class="bi bi-tv me-2"></i>Preview TV
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('playlists.destroy', $playlist) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus playlist ini?')"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        @if($playlist->description)
                            <p class="card-text text-muted small">{{ Str::limit($playlist->description, 100) }}</p>
                        @endif

                        <div class="playlist-info mb-3">
                            <span class="badge bg-{{ $playlist->is_active ? 'success' : 'secondary' }} me-2">
                                {{ $playlist->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>

                            @if($playlist->auto_play)
                                <span class="badge bg-info me-2">Auto Play</span>
                            @endif

                            @if($playlist->loop_playlist)
                                <span class="badge bg-warning text-dark">Loop</span>
                            @endif
                        </div>

                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-primary fw-bold">{{ $playlist->videos->count() }}</div>
                                <small class="text-muted">Videos</small>
                            </div>
                            <div class="col-6">
                                <div class="text-success fw-bold">{{ $playlist->formatted_total_duration ?? '00:00' }}</div>
                                <small class="text-muted">Durasi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $playlists->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-collection-play text-muted" style="font-size: 4rem;"></i>
        <h4 class="text-muted mt-3">Belum ada playlist</h4>
        <p class="text-muted">Mulai dengan membuat playlist pertama Anda.</p>
        <a href="{{ route('playlists.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Buat Playlist
        </a>
    </div>
@endif
@endsection
