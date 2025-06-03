@extends('layouts.app')

@section('title', 'Tambah Video Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Video Baru
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('videos.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Video *</label>
                        <input type="text"
                               class="form-control @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="video_url" class="form-label">URL Video *</label>
                        <input type="url"
                               class="form-control @error('video_url') is-invalid @enderror"
                               id="video_url"
                               name="video_url"
                               value="{{ old('video_url') }}"
                               placeholder="https://example.com/video.mp4"
                               required>
                        @error('video_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Masukkan URL lengkap ke file video (mp4, avi, dll)</div>
                    </div>

                    <div class="mb-3">
                        <label for="thumbnail_url" class="form-label">URL Thumbnail</label>
                        <input type="url"
                               class="form-control @error('thumbnail_url') is-invalid @enderror"
                               id="thumbnail_url"
                               name="thumbnail_url"
                               value="{{ old('thumbnail_url') }}"
                               placeholder="https://example.com/thumbnail.jpg">
                        @error('thumbnail_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">URL gambar untuk thumbnail video (opsional)</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="duration" class="form-label">Durasi (detik)</label>
                            <input type="number"
                                   class="form-control @error('duration') is-invalid @enderror"
                                   id="duration"
                                   name="duration"
                                   value="{{ old('duration') }}"
                                   min="0"
                                   placeholder="0">
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="video_type" class="form-label">Tipe Video</label>
                            <select class="form-select @error('video_type') is-invalid @enderror"
                                    id="video_type"
                                    name="video_type">
                                <option value="mp4" {{ old('video_type') == 'mp4' ? 'selected' : '' }}>MP4</option>
                                <option value="avi" {{ old('video_type') == 'avi' ? 'selected' : '' }}>AVI</option>
                                <option value="mov" {{ old('video_type') == 'mov' ? 'selected' : '' }}>MOV</option>
                                <option value="wmv" {{ old('video_type') == 'wmv' ? 'selected' : '' }}>WMV</option>
                                <option value="webm" {{ old('video_type') == 'webm' ? 'selected' : '' }}>WebM</option>
                            </select>
                            @error('video_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="sort_order" class="form-label">Urutan Tampil</label>
                            <input type="number"
                                   class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order"
                                   name="sort_order"
                                   value="{{ old('sort_order', 0) }}"
                                   min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Video Aktif
                            </label>
                            <div class="form-text">Video aktif akan tersedia untuk ditambahkan ke playlist</div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('videos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Simpan Video
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-detect video duration from URL (if supported)
document.getElementById('video_url').addEventListener('blur', function() {
    const url = this.value;
    if (url && url.match(/\.(mp4|avi|mov|wmv|webm)$/i)) {
        // Create temporary video element to get duration
        const video = document.createElement('video');
        video.preload = 'metadata';
        video.src = url;

        video.addEventListener('loadedmetadata', function() {
            if (this.duration && this.duration !== Infinity) {
                document.getElementById('duration').value = Math.round(this.duration);
            }
        });

        video.addEventListener('error', function() {
            console.log('Could not load video metadata');
        });
    }
});
</script>
@endpush
