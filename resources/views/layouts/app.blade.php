<!DOCTYPE html>
<html lang="en" class="{{ session('dark_mode') ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Disty Teknologi' }}</title>
    <link rel="stylesheet" href="{{ asset('css/disty.css') }}">
</head>
<body class="{{ session('dark_mode') ? 'is-dark' : '' }}">
    <div class="app-shell">
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <button type="button" class="icon-button" onclick="toggleSidebar()">←</button>
                <a href="{{ route('dashboard') }}" class="brand small">
                    <span class="brand-mark"></span>
                    <span>Disty Teknologi</span>
                </a>
            </div>

            <nav class="side-nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span>⊞</span> Dashboard
                </a>

                <a href="{{ route('chat.index') }}" class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <span>◉</span> Chat
                </a>

                <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">
                    <span>♟</span> Staff
                </a>
            </nav>

            <div class="side-bottom">
                <a href="#" class="logout">
                    <span>↪</span> Logout
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="top-left">
                    <button type="button" class="hamburger" onclick="toggleSidebar()">☰</button>

                    <a href="{{ route('home') }}" class="brand">
                        <span class="brand-mark"></span>
                        <span>Disty Teknologi</span>
                    </a>

                    <span class="divider"></span>

                    <span class="page-label">{{ $pageLabel ?? 'Home' }}</span>
                </div>

                <div class="profile-icon">♙</div>
            </header>

            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <section class="page-body">
                @yield('content')
            </section>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }
    </script>
</body>
</html>