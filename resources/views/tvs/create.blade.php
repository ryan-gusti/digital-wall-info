@extends('layouts.app')

@section('title', 'Tambah TV Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah TV Baru
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('tvs.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama TV *</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address *</label>
                        <input type="text"
                               class="form-control @error('ip_address') is-invalid @enderror"
                               id="ip_address"
                               name="ip_address"
                               value="{{ old('ip_address') }}"
                               placeholder="192.168.1.100"
                               required>
                        @error('ip_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Masukkan IP address TV yang valid</div>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Lokasi</label>
                        <input type="text"
                               class="form-control @error('location') is-invalid @enderror"
                               id="location"
                               name="location"
                               value="{{ old('location') }}"
                               placeholder="Ruang Meeting A, Lobby Utama, dll">
                        @error('location')
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
                        <label for="playlist_id" class="form-label">Playlist</label>
                        <select class="form-select @error('playlist_id') is-invalid @enderror"
                                id="playlist_id"
                                name="playlist_id">
                            <option value="">-- Pilih Playlist (Opsional) --</option>
                            @foreach($playlists as $playlist)
                                <option value="{{ $playlist->id }}" 
                                        {{ old('playlist_id') == $playlist->id ? 'selected' : '' }}>
                                    {{ $playlist->name }} ({{ $playlist->videos->count() }} video)
                                </option>
                            @endforeach
                        </select>
                        @error('playlist_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Playlist yang akan otomatis dimainkan ketika TV mengakses sistem</div>
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
                                TV Aktif
                            </label>
                            <div class="form-text">TV aktif dapat mengakses sistem dan memutar playlist</div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tvs.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Simpan TV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
