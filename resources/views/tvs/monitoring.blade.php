@extends('layouts.app')

@section('title', 'TV Monitoring')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">
        <i class="bi bi-tv me-2"></i>
        TV Monitoring Dashboard
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('tvs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list me-1"></i>
            TV Management
        </a>
        <button class="btn btn-primary" onclick="refreshPage()">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Refresh
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-tv text-primary" style="font-size: 2rem;"></i>
                <h3 class="mt-2">{{ $stats['total'] }}</h3>
                <p class="text-muted mb-0">Total TVs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                <h3 class="mt-2">{{ $stats['active'] }}</h3>
                <p class="text-muted mb-0">Active TVs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-wifi text-success" style="font-size: 2rem;"></i>
                <h3 class="mt-2">{{ $stats['online'] }}</h3>
                <p class="text-muted mb-0">Online TVs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-wifi-off text-danger" style="font-size: 2rem;"></i>
                <h3 class="mt-2">{{ $stats['offline'] }}</h3>
                <p class="text-muted mb-0">Offline TVs</p>
            </div>
        </div>
    </div>
</div>

@if($tvs->count() > 0)
    <!-- Online TVs -->
    @php
        $onlineTvs = $tvs->filter(function($tv) { return $tv->isOnline() && $tv->is_active; });
    @endphp

    @if($onlineTvs->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-wifi me-2"></i>
                    Online TVs ({{ $onlineTvs->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>TV Name</th>
                                <th>Location</th>
                                <th>IP Address</th>
                                <th>Playlist</th>
                                <th>Last Seen</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($onlineTvs as $tv)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $tv->name }}</div>
                                        @if($tv->description)
                                            <small class="text-muted">{{ Str::limit($tv->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $tv->location ?: '-' }}</td>
                                    <td><code class="bg-light px-2 py-1 rounded">{{ $tv->ip_address }}</code></td>
                                    <td>
                                        @if($tv->playlist)
                                            <span class="badge bg-success">{{ $tv->playlist->name }}</span>
                                            <br><small class="text-muted">{{ $tv->playlist->videos->count() }} video(s)</small>
                                        @else
                                            <span class="badge bg-secondary">No playlist</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-success small">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                            {{ $tv->getLastSeenFormatted() }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            {{ $tv->last_seen->format('M j, Y H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tvs.show', $tv) }}" class="btn btn-outline-info" title="Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($tv->playlist)
                                                <a href="{{ route('tv.play', $tv->playlist) }}" target="_blank" class="btn btn-outline-success" title="Preview">
                                                    <i class="bi bi-play-circle"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Offline TVs -->
    @php
        $offlineTvs = $tvs->filter(function($tv) { return !$tv->isOnline() && $tv->is_active; });
    @endphp

    @if($offlineTvs->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-wifi-off me-2"></i>
                    Offline TVs ({{ $offlineTvs->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>TV Name</th>
                                <th>Location</th>
                                <th>IP Address</th>
                                <th>Playlist</th>
                                <th>Last Seen</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offlineTvs as $tv)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $tv->name }}</div>
                                        @if($tv->description)
                                            <small class="text-muted">{{ Str::limit($tv->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $tv->location ?: '-' }}</td>
                                    <td><code class="bg-light px-2 py-1 rounded">{{ $tv->ip_address }}</code></td>
                                    <td>
                                        @if($tv->playlist)
                                            <span class="badge bg-success">{{ $tv->playlist->name }}</span>
                                            <br><small class="text-muted">{{ $tv->playlist->videos->count() }} video(s)</small>
                                        @else
                                            <span class="badge bg-secondary">No playlist</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tv->last_seen)
                                            <div class="text-danger small">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                                {{ $tv->getLastSeenFormatted() }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                {{ $tv->last_seen->format('M j, Y H:i') }}
                                            </div>
                                        @else
                                            <span class="text-muted small">Never connected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tvs.show', $tv) }}" class="btn btn-outline-info" title="Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('tvs.edit', $tv) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Inactive TVs -->
    @php
        $inactiveTvs = $tvs->filter(function($tv) { return !$tv->is_active; });
    @endphp

    @if($inactiveTvs->count() > 0)
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-x-circle me-2"></i>
                    Inactive TVs ({{ $inactiveTvs->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>TV Name</th>
                                <th>Location</th>
                                <th>IP Address</th>
                                <th>Last Seen</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inactiveTvs as $tv)
                                <tr class="table-secondary">
                                    <td>
                                        <div class="fw-bold text-muted">{{ $tv->name }}</div>
                                        @if($tv->description)
                                            <small class="text-muted">{{ Str::limit($tv->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $tv->location ?: '-' }}</td>
                                    <td><code class="bg-secondary text-white px-2 py-1 rounded">{{ $tv->ip_address }}</code></td>
                                    <td>
                                        @if($tv->last_seen)
                                            <div class="text-muted small">
                                                {{ $tv->getLastSeenFormatted() }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                {{ $tv->last_seen->format('M j, Y H:i') }}
                                            </div>
                                        @else
                                            <span class="text-muted small">Never connected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tvs.show', $tv) }}" class="btn btn-outline-info" title="Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('tvs.edit', $tv) }}" class="btn btn-outline-warning" title="Activate">
                                                <i class="bi bi-power"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@else
    <div class="text-center py-5">
        <i class="bi bi-tv text-muted" style="font-size: 6rem;"></i>
        <h3 class="text-muted mt-4">No TVs registered</h3>
        <p class="text-muted">Register your first TV to start monitoring.</p>
        <a href="{{ route('tvs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Add TV
        </a>
    </div>
@endif
@endsection

@push('scripts')
<script>
function refreshPage() {
    window.location.reload();
}

// Auto refresh every 30 seconds
setInterval(function() {
    window.location.reload();
}, 30000);

// Show loading indicator when refreshing
window.addEventListener('beforeunload', function() {
    document.body.style.cursor = 'wait';
});
</script>
@endpush
