@extends('layouts.app')

@section('title', 'Video Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-camera-video me-2"></i>
        Video Management
    </h1>
    <a href="{{ route('videos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Tambah Video
    </a>
</div>

@if($videos->count() > 0)
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Thumbnail</th>
                            <th>Judul</th>
                            <th style="width: 100px;">Durasi</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 100px;">Urutan</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($videos as $video)
                            <tr>
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
                                    <span class="badge bg-{{ $video->is_active ? 'success' : 'secondary' }}">
                                        {{ $video->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $video->sort_order }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('videos.show', $video) }}"
                                           class="btn btn-outline-info"
                                           title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('videos.edit', $video) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('videos.destroy', $video) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus video ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-outline-danger"
                                                    title="Hapus">
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
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $videos->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-camera-video text-muted" style="font-size: 4rem;"></i>
        <h4 class="text-muted mt-3">Belum ada video</h4>
        <p class="text-muted">Mulai dengan menambahkan video pertama Anda.</p>
        <a href="{{ route('videos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Video
        </a>
    </div>
@endif
@endsection
