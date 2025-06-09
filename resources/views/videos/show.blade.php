@extends('layouts.app')

@section('title', $video->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ $video->title }}</h1>
                <div>
                    <a href="{{ route('videos.edit', $video) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('videos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Videos
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Video Player</h5>
                        </div>
                        <div class="card-body p-0">
                            <video controls style="width: 100%; height: auto;" id="videoPlayer">
                                <source src="{{ $video->videoUrl() }}" type="video/{{ $video->video_type }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>

                    @if($video->playlists->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Used in Playlists</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($video->playlists as $playlist)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('playlists.show', $playlist) }}" class="text-decoration-none">
                                                    {{ $playlist->name }}
                                                </a>
                                            </h6>
                                            @if($playlist->description)
                                            <small class="text-muted">{{ Str::limit($playlist->description, 100) }}</small>
                                            @endif
                                        </div>
                                        <div class="ms-2">
                                            @if($playlist->is_active)
                                            <span class="badge bg-success">Active</span>
                                            @else
                                            <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Video Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Title:</strong></td>
                                    <td>{{ $video->title }}</td>
                                </tr>
                                @if($video->description)
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $video->description }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ strtoupper($video->video_type) }}</span>
                                    </td>
                                </tr>
                                @if($video->duration)
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $video->formatted_duration }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($video->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Sort Order:</strong></td>
                                    <td>{{ $video->sort_order }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $video->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $video->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>

                            @if($video->thumbnail_url)
                            <div class="mt-3">
                                <strong>Thumbnail:</strong>
                                <div class="mt-2">
                                    <img src="{{ $video->thumbnail_url }}" alt="Thumbnail" class="img-thumbnail" style="max-width: 100%;">
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('videos.edit', $video) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Video
                                </a>

                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addToPlaylistModal">
                                    <i class="fas fa-plus me-2"></i>Add to Playlist
                                </button>

                                <button type="button" class="btn btn-danger" onclick="deleteVideo()">
                                    <i class="fas fa-trash me-2"></i>Delete Video
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Video Statistics -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary">{{ $video->playlists->count() }}</h4>
                                        <small class="text-muted">Playlists</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">{{ $video->playlists->where('is_active', true)->count() }}</h4>
                                    <small class="text-muted">Active Playlists</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add to Playlist Modal -->
<div class="modal fade" id="addToPlaylistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Video to Playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addToPlaylistForm">
                    @csrf
                    <div class="mb-3">
                        <label for="playlist_id" class="form-label">Select Playlist</label>
                        <select class="form-select" id="playlist_id" name="playlist_id" required>
                            <option value="">Choose a playlist...</option>
                            @foreach(\App\Models\Playlist::all() as $playlist)
                                @if(!$video->playlists->contains($playlist->id))
                                <option value="{{ $playlist->id }}">{{ $playlist->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addToPlaylist()">Add to Playlist</button>
            </div>
        </div>
    </div>
</div>

<script>
function deleteVideo() {
    if (confirm('Are you sure you want to delete this video? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("videos.destroy", $video) }}';

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

function addToPlaylist() {
    const playlistId = document.getElementById('playlist_id').value;
    if (!playlistId) {
        alert('Please select a playlist');
        return;
    }

    fetch(`/playlists/${playlistId}/videos`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            video_id: {{ $video->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Video added to playlist successfully!');
            location.reload();
        } else {
            alert('Error adding video to playlist: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Auto-play functionality
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('videoPlayer');

    video.addEventListener('error', function(e) {
        console.error('Video load error:', e);
        const errorMsg = document.createElement('div');
        errorMsg.className = 'alert alert-danger mt-3';
        errorMsg.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Unable to load video. Please check the video URL.';
        video.parentNode.appendChild(errorMsg);
    });
});
</script>
@endsection
