@extends('layouts.app')

@section('title', 'Detail Playlist - ' . $playlist->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-collection-play me-2"></i>
        {{ $playlist->name }}
    </h1>
    <div class="btn-group">
        <a href="{{ route('playlists.edit', $playlist) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>
            Edit
        </a>
        <a href="{{ route('tv.play', $playlist) }}" class="btn btn-success" target="_blank">
            <i class="bi bi-tv me-1"></i>
            Preview TV
        </a>
    </div>
</div>

<!-- Playlist Info -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Informasi Playlist</h5>
                @if($playlist->description)
                    <p class="card-text">{{ $playlist->description }}</p>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $playlist->is_active ? 'success' : 'secondary' }} ms-2">
                            {{ $playlist->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Total Durasi:</strong>
                        <span class="badge bg-info ms-2">{{ $playlist->formatted_total_duration ?? '00:00' }}</span>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>Auto Play:</strong>
                        <span class="badge bg-{{ $playlist->auto_play ? 'primary' : 'secondary' }} ms-2">
                            {{ $playlist->auto_play ? 'Ya' : 'Tidak' }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Loop Playlist:</strong>
                        <span class="badge bg-{{ $playlist->loop_playlist ? 'warning' : 'secondary' }} ms-2">
                            {{ $playlist->loop_playlist ? 'Ya' : 'Tidak' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h2 class="text-primary">{{ $playlist->videos->count() }}</h2>
                <p class="card-text">Total Video dalam Playlist</p>
                <a href="#add-video" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Video
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Video List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-camera-video me-2"></i>
            Daftar Video ({{ $playlist->videos->count() }})
        </h5>
    </div>
    <div class="card-body p-0">
        @if($playlist->videos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Urutan</th>
                            <th style="width: 80px;">Thumbnail</th>
                            <th>Judul</th>
                            <th style="width: 100px;">Durasi</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($playlist->videos as $video)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $video->pivot->sort_order }}</span>
                                </td>
                                <td>
                                    @if($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}"
                                             alt="Thumbnail"
                                             class="img-thumbnail"
                                             style="width: 60px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                             style="width: 60px; height: 40px;">
                                            <i class="bi bi-camera-video text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $video->title }}</div>
                                    @if($video->description)
                                        <small class="text-muted">{{ Str::limit($video->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $video->formatted_duration ?? '00:00' }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('videos.show', $video) }}"
                                           class="btn btn-outline-info"
                                           title="Detail Video">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('playlists.remove-video', [$playlist, $video]) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Hapus video dari playlist?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-outline-danger"
                                                    title="Hapus dari Playlist">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-camera-video text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Playlist masih kosong</h5>
                <p class="text-muted">Tambahkan video ke playlist ini.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Video Pertama
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Add Video Modal -->
<div class="modal fade" id="addVideoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Video ke Playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('playlists.add-video', $playlist) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="video_id" class="form-label">Pilih Video</label>
                        <select class="form-select" id="video_id" name="video_id" required>
                            <option value="">-- Pilih Video --</option>
                            @foreach($availableVideos as $video)
                                @if(!$playlist->videos->contains($video->id))
                                    <option value="{{ $video->id }}">
                                        {{ $video->title }}
                                        @if($video->formatted_duration)
                                            ({{ $video->formatted_duration }})
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Urutan dalam Playlist</label>
                        <input type="number"
                               class="form-control"
                               id="sort_order"
                               name="sort_order"
                               value="{{ $playlist->videos->count() + 1 }}"
                               min="1">
                        <div class="form-text">Urutan video dalam playlist</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah ke Playlist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('playlists.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali ke Daftar Playlist
    </a>
</div>
@endsection
