<!doctype html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="supabase-upload-sign-url" content="{{ route('media.sign') }}">
    <title>@yield('title', 'Dashboard') · YUSTERAPI</title>
    <link rel="stylesheet" href="{{ asset('css/yusterapi.css') }}?v=2026112001">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">Y</span><span><strong>YUSTERAPI</strong><small>Customer Management</small></span></a>
            <div class="side-label">MENU UTAMA</div>
            <nav aria-label="Navigasi utama">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>◫</span> Dashboard</a>
                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><span>♧</span> Pelanggan</a>
                <a class="nav-link {{ request()->routeIs('health-history.*') ? 'active' : '' }}" href="{{ route('health-history.index') }}"><span>✚</span> Sejarah Kesihatan</a>
                <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}"><span>▦</span> Booking</a>
                <a class="nav-link {{ request()->routeIs('appointment-requests.*') ? 'active' : '' }}" href="{{ route('appointment-requests.index') }}"><span>✉</span> Permintaan Baru</a>
                <a class="nav-link {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}"><span>✣</span> Pakej Rawatan</a>
                <a class="nav-link {{ request()->routeIs('landing-page.*') ? 'active' : '' }}" href="{{ route('landing-page.edit') }}"><span>✎</span> Edit Landing Page</a>
                <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}"><span>◇</span> Bayaran</a>
                <a class="nav-link {{ request()->routeIs('notes.*') ? 'active' : '' }}" href="{{ route('notes.index') }}"><span>▤</span> Catatan</a>
            </nav>
            <div class="sidebar-bottom"><div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><strong>{{ auth()->user()->name }}</strong><small>Staf YUSTERAPI</small></div><form method="post" action="{{ route('logout') }}">@csrf<button type="submit" title="Log keluar" aria-label="Log keluar">↗</button></form></div>
        </aside>
        <main class="main">
            <div class="topbar"><div class="mobile-brand">YUSTERAPI</div><div class="topbar-right"><a href="{{ route('home') }}" target="_blank" rel="noopener">Lihat laman awam ↗</a><span class="topbar-dot"></span><span class="topbar-date">{{ now()->translatedFormat('l, d F Y') }}</span><span class="topbar-dot"></span> Sistem Pengurusan Pelanggan</div></div>
            <div class="content">
                @if(session('success'))<div class="alert success" role="status">{{ session('success') }}</div>@endif
                @if($errors->any())<div class="alert error" role="alert"><strong>Sila semak maklumat:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </div>
        </main>
    </div>
    <script src="{{ asset('js/supabase-image-upload.js') }}?v=2026111903" defer></script>
</body>
</html>
