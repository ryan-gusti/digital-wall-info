@extends('layouts.app')

@section('title', 'Edit Playlist - ' . $playlist->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Playlist: {{ $playlist->name }}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('playlists.update', $playlist) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Playlist *</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $playlist->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3">{{ old('description', $playlist->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sort_order" class="form-label">Urutan Tampil</label>
                            <input type="number"
                                   class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order"
                                   name="sort_order"
                                   value="{{ old('sort_order', $playlist->sort_order) }}"
                                   min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Semakin kecil nomor, semakin di atas urutan tampil</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $playlist->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Playlist Aktif
                            </label>
                            <div class="form-text">Hanya playlist aktif yang akan ditampilkan di Smart TV</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="auto_play"
                                   name="auto_play"
                                   value="1"
                                   {{ old('auto_play', $playlist->auto_play) ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_play">
                                Auto Play
                            </label>
                            <div class="form-text">Otomatis putar video selanjutnya</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="loop_playlist"
                                   name="loop_playlist"
                                   value="1"
                                   {{ old('loop_playlist', $playlist->loop_playlist) ? 'checked' : '' }}>
                            <label class="form-check-label" for="loop_playlist">
                                Loop Playlist
                            </label>
                            <div class="form-text">Ulangi playlist setelah video terakhir selesai</div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('playlists.show', $playlist) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Update Playlist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
