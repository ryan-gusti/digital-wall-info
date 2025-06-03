@extends('layouts.app')

@section('title', 'Edit Video')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Edit Video</h1>
                <a href="{{ route('videos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Videos
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('videos.update', $video) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title', $video->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3">{{ old('description', $video->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="video_url" class="form-label">Video URL <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control @error('video_url') is-invalid @enderror"
                                           id="video_url" name="video_url" value="{{ old('video_url', $video->video_url) }}" required>
                                    @error('video_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Enter the direct URL to the video file (MP4, WebM, etc.)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="thumbnail_url" class="form-label">Thumbnail URL</label>
                                    <input type="url" class="form-control @error('thumbnail_url') is-invalid @enderror"
                                           id="thumbnail_url" name="thumbnail_url" value="{{ old('thumbnail_url', $video->thumbnail_url) }}">
                                    @error('thumbnail_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Optional: Enter the URL for video thumbnail image</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (seconds)</label>
                                    <input type="number" class="form-control @error('duration') is-invalid @enderror"
                                           id="duration" name="duration" value="{{ old('duration', $video->duration) }}" min="1">
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty for auto-detection</div>
                                </div>

                                <div class="mb-3">
                                    <label for="video_type" class="form-label">Video Type</label>
                                    <select class="form-select @error('video_type') is-invalid @enderror" id="video_type" name="video_type">
                                        <option value="mp4" {{ old('video_type', $video->video_type) == 'mp4' ? 'selected' : '' }}>MP4</option>
                                        <option value="webm" {{ old('video_type', $video->video_type) == 'webm' ? 'selected' : '' }}>WebM</option>
                                        <option value="avi" {{ old('video_type', $video->video_type) == 'avi' ? 'selected' : '' }}>AVI</option>
                                        <option value="mov" {{ old('video_type', $video->video_type) == 'mov' ? 'selected' : '' }}>MOV</option>
                                        <option value="other" {{ old('video_type', $video->video_type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('video_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $video->sort_order) }}" min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                               {{ old('is_active', $video->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>

                                <!-- Video Preview -->
                                @if($video->video_url)
                                <div class="mb-3">
                                    <label class="form-label">Current Video Preview</label>
                                    <div class="border rounded p-2">
                                        <video controls style="width: 100%; max-height: 200px;">
                                            <source src="{{ $video->video_url }}" type="video/{{ $video->video_type }}">
                                            Your browser does not support the video tag.
                                        </video>
                                        @if($video->duration)
                                        <small class="text-muted d-block mt-1">Duration: {{ $video->formatted_duration }}</small>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Video
                            </button>
                            <a href="{{ route('videos.show', $video) }}" class="btn btn-info">
                                <i class="fas fa-eye me-2"></i>View Video
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoUrlInput = document.getElementById('video_url');
    const durationInput = document.getElementById('duration');

    videoUrlInput.addEventListener('blur', function() {
        if (this.value && !durationInput.value) {
            // Try to auto-detect duration
            const video = document.createElement('video');
            video.src = this.value;
            video.addEventListener('loadedmetadata', function() {
                if (video.duration && !isNaN(video.duration)) {
                    durationInput.value = Math.round(video.duration);
                }
            });
        }
    });
});
</script>
@endsection
