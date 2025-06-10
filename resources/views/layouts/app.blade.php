<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digital Wall Info - Video Playlist System')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .video-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }
        .playlist-info {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('playlists.index') }}">
                <i class="bi bi-play-circle-fill me-2"></i>
                Digital Wall Info
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('playlists.*') ? 'active' : '' }}"
                           href="{{ route('playlists.index') }}">
                            <i class="bi bi-collection-play me-1"></i>
                            Playlists
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('videos.*') ? 'active' : '' }}"
                           href="{{ route('videos.index') }}">
                            <i class="bi bi-camera-video me-1"></i>
                            Videos
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('tvs.*') ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-display me-1"></i>
                            TV Management
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('tvs.index') ? 'active' : '' }}"
                                   href="{{ route('tvs.index') }}">
                                    <i class="bi bi-list me-2"></i>
                                    TV List
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('tvs.monitoring') ? 'active' : '' }}"
                                   href="{{ route('tvs.monitoring') }}">
                                    <i class="bi bi-activity me-2"></i>
                                    TV Monitoring
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('tvs.create') }}">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Add New TV
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tv.index') }}" target="_blank">
                            <i class="bi bi-tv me-1"></i>
                            Smart TV View
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-4">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-light mt-5 py-4">
        <div class="container text-center text-muted">
            <p>&copy; {{ date('Y') }} Digital Wall Info. Sistem Informasi Playlist Video untuk Smart TV.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
