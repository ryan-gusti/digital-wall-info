@extends('layouts.app')

@section('title', 'Detail TV - ' . $tv->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-tv me-2"></i>
        {{ $tv->name }}
    </h1>
    <div class="btn-group">
        <a href="{{ route('tvs.edit', $tv) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>
            Edit
        </a>
        @if($tv->playlist)
            <a href="{{ route('tv.play', $tv->playlist) }}" class="btn btn-success" target="_blank">
                <i class="bi bi-play-circle me-1"></i>
                Preview Playlist
            </a>
        @endif
    </div>
</div>

<!-- TV Info -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi TV
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Nama TV:</strong>
                        <div class="mt-1">{{ $tv->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <strong>IP Address:</strong>
                        <div class="mt-1">
                            <code class="bg-light px-2 py-1 rounded">{{ $tv->ip_address }}</code>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Lokasi:</strong>
                        <div class="mt-1">{{ $tv->location ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <div class="mt-1">
                            <span class="badge bg-{{ $tv->is_active ? 'success' : 'secondary' }}">
                                {{ $tv->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($tv->description)
                <div class="row">
                    <div class="col-12">
                        <strong>Deskripsi:</strong>
                        <div class="mt-1">{{ $tv->description }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-collection-play me-2"></i>
                    Playlist
                </h5>
            </div>
            <div class="card-body text-center">
                @if($tv->playlist)
                    <h4 class="text-primary">{{ $tv->playlist->name }}</h4>
                    <p class="text-muted mb-3">{{ $tv->playlist->videos->count() }} video dalam playlist</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('playlists.show', $tv->playlist) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>
                            Lihat Detail Playlist
                        </a>
                        <a href="{{ route('tv.play', $tv->playlist) }}" class="btn btn-primary btn-sm" target="_blank">
                            <i class="bi bi-play-circle me-1"></i>
                            Preview Playlist
                        </a>
                    </div>

                    @if($tv->playlist->description)
                        <div class="mt-3">
                            <small class="text-muted">{{ $tv->playlist->description }}</small>
                        </div>
                    @endif
                @else
                    <i class="bi bi-collection-play text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Tidak ada playlist</h5>
                    <p class="text-muted">TV ini belum memiliki playlist yang ditentukan.</p>
                    <a href="{{ route('tvs.edit', $tv) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>
                        Pilih Playlist
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@if($tv->playlist && $tv->playlist->videos->count() > 0)
<!-- Video List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-camera-video me-2"></i>
            Daftar Video dalam Playlist ({{ $tv->playlist->videos->count() }})
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">Urutan</th>
                        <th style="width: 80px;">Thumbnail</th>
                        <th>Judul</th>
                        <th style="width: 100px;">Durasi</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tv->playlist->videos as $video)
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
                                <a href="{{ route('videos.show', $video) }}"
                                   class="btn btn-outline-info btn-sm"
                                   title="Detail Video">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="mt-4">
    <a href="{{ route('tvs.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali ke Daftar TV
    </a>
</div>
@endsection
