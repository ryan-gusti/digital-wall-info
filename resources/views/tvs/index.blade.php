@extends('layouts.app')

@section('title', 'TV Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">    <h1 class="h2">
        <i class="bi bi-tv me-2"></i>
        TV Management
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('tvs.monitoring') }}" class="btn btn-outline-success">
            <i class="bi bi-activity me-1"></i>
            TV Monitoring
        </a>
        <a href="{{ route('tvs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah TV
        </a>
    </div>
</div>

@if($tvs->count() > 0)
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">                        <tr>
                            <th>Nama TV</th>
                            <th>IP Address</th>
                            <th>Lokasi</th>
                            <th>Playlist</th>
                            <th>Status</th>
                            <th>Last Seen</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tvs as $tv)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $tv->name }}</div>
                                    @if($tv->description)
                                        <small class="text-muted">{{ Str::limit($tv->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $tv->ip_address }}</code>
                                </td>
                                <td>
                                    {{ $tv->location ?: '-' }}
                                </td>
                                <td>
                                    @if($tv->playlist)
                                        <span class="badge bg-success">{{ $tv->playlist->name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $tv->playlist->videos->count() }} video(s)</small>
                                    @else
                                        <span class="badge bg-secondary">Tidak ada playlist</span>
                                    @endif                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-{{ $tv->is_active ? 'success' : 'secondary' }}">
                                            {{ $tv->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                        @if($tv->is_active)
                                            <span class="badge bg-{{ $tv->isOnline() ? 'success' : 'danger' }}">
                                                <i class="bi bi-{{ $tv->isOnline() ? 'wifi' : 'wifi-off' }} me-1"></i>
                                                {{ $tv->isOnline() ? 'Online' : 'Offline' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($tv->last_seen)
                                        <div class="small">
                                            <div class="text-muted">{{ $tv->getLastSeenFormatted() }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                {{ $tv->last_seen->format('M j, Y H:i') }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">Never connected</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tvs.show', $tv) }}"
                                           class="btn btn-outline-info"
                                           title="Detail TV">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('tvs.edit', $tv) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit TV">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($tv->playlist)
                                            <a href="{{ route('tv.play', $tv->playlist) }}"
                                               target="_blank"
                                               class="btn btn-outline-success"
                                               title="Preview Playlist">
                                                <i class="bi bi-play-circle"></i>
                                            </a>
                                        @endif
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                title="Hapus TV"
                                                onclick="deleteTV({{ $tv->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
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
        {{ $tvs->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-tv text-muted" style="font-size: 4rem;"></i>
        <h4 class="text-muted mt-3">Belum ada TV yang terdaftar</h4>
        <p class="text-muted">Mulai dengan menambahkan TV pertama Anda.</p>
        <a href="{{ route('tvs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah TV
        </a>
    </div>
@endif
@endsection

@push('scripts')
<script>
function deleteTV(tvId) {
    if (confirm('Yakin ingin menghapus TV ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tvs/${tvId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
