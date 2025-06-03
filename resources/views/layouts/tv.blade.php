<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart TV - Digital Wall Info')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .tv-container {
            padding: 2rem 0;
        }

        .playlist-card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .playlist-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .playlist-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 15px 15px 0 0;
        }

        .video-player {
            width: 100%;
            height: 70vh;
            background: #000;
            border-radius: 15px;
        }

        .control-panel {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        .control-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            font-size: 24px;
            margin: 0 10px;
            transition: all 0.3s ease;
            background: #fff;
            color: #333;
        }

        .control-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        .control-btn.active {
            background: #28a745;
            color: white;
        }

        .video-info {
            color: white;
            text-align: center;
        }

        .playlist-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .video-title {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .video-counter {
            font-size: 1.2rem;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .control-btn {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="tv-container">
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
