<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard') — Vedha Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ optional(\App\Models\GeneralSetting::current())->favicon_url }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple: #7C3AED;
            --purple-dark: #6D28D9;
            --purple-light: #F3E8FF;
            --text: #1F2937;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --bg: #F8F9FC;
            --danger: #DC2626;
            --green: #059669;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Poppins', sans-serif; background: var(--bg); color: var(--text); }
        a { text-decoration: none; color: inherit; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #fff; border-right: 1px solid var(--border); padding: 24px 16px; flex-shrink: 0; }
        .sidebar .logo { font-size: 26px; font-weight: 700; color: var(--purple); margin-bottom: 24px; padding: 0 8px; }
        .nav-link { display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 10px; color: var(--text-muted); font-size: 14.5px; font-weight: 500; margin-bottom: 2px; }
        .nav-link.active, .nav-link:hover { background: var(--purple-light); color: var(--purple); }
        .nav-divider { border-top: 1px solid var(--border); margin: 14px 0; }
        .main { flex: 1; min-width: 0; }
        .topbar { display: flex; justify-content: space-between; align-items: center; padding: 18px 32px; background: #fff; border-bottom: 1px solid var(--border); }
        .grid-icon { width: 36px; height: 36px; border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .user-chip { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 14px; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #E5E7EB; display:flex; align-items:center; justify-content:center; }
        .content { padding: 28px 32px; }
        .card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 24px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card-title { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .card-title .dot { color: var(--purple); }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; text-decoration: none; }
        .btn-primary { background: var(--purple); color: #fff; }
        .btn-primary:hover { background: var(--purple-dark); }
        .btn-secondary { background: #F3F4F6; color: var(--text); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-outline { background: #fff; border: 1px solid var(--purple); color: var(--purple); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 10px; font-size: 12px; letter-spacing: .04em; color: var(--text-muted); border-bottom: 1px solid var(--border); text-transform: uppercase; }
        td { padding: 14px 10px; font-size: 14px; border-bottom: 1px solid var(--border); }
        tr:nth-child(even) td { background: #FAFAFC; }
        .icon-btn { width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border); background: #fff; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; margin-left: 4px; }
        .icon-btn.edit { color: var(--green); }
        .icon-btn.delete { color: var(--danger); }
        input[type=text], input[type=email], input[type=password], input[type=number], input[type=url], select, textarea {
            width: 100%; padding: 11px 14px; border: 1px solid var(--border); border-radius: 10px; font-size: 14px; font-family: inherit; color: var(--text);
        }
        label { font-size: 13.5px; font-weight: 500; display:block; margin-bottom: 6px; margin-top: 14px; }
        .toggle { width: 40px; height: 22px; border-radius: 100px; background: #D1D5DB; position: relative; cursor: pointer; display:inline-block; }
        .toggle.on { background: var(--purple); }
        .toggle .knob { width: 18px; height: 18px; border-radius: 50%; background: #fff; position: absolute; top: 2px; left: 2px; transition: .15s; }
        .toggle.on .knob { left: 20px; }
        .modal-backdrop { position: fixed; inset: 0; background: rgba(17,17,17,.5); display:flex; align-items:center; justify-content:center; z-index: 50; }
        .modal { background:#fff; border-radius: 16px; padding: 26px; width: 460px; max-width: 92vw; max-height: 88vh; overflow-y:auto; }
        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 6px; }
        .badge { padding: 4px 10px; border-radius: 100px; font-size: 12px; font-weight: 600; background: var(--purple-light); color: var(--purple); }
        .pagination { display:flex; gap:6px; justify-content:flex-end; margin-top: 16px; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; border: 1px solid var(--border); font-size: 13px; color: var(--text); }
        .pagination .active { background: var(--purple); color:#fff; border-color: var(--purple); }
        .alert-success { background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 16px; border-radius: 10px; margin-bottom: 18px; }
        .search-input { width: 260px; }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="logo">{{ optional(\App\Models\GeneralSetting::current())->title ?? 'Vedha' }}</div>

        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">📊 Dashboard</a>
        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">🗂️ Categories</a>
        <a class="nav-link {{ request()->routeIs('admin.authors.*') ? 'active' : '' }}" href="{{ route('admin.authors.index') }}">👤 Author</a>
        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">👥 Customers</a>
        <a class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}" href="{{ route('admin.books.index') }}">📖 Book</a>
        <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">⭐ Book Review</a>
        <a class="nav-link {{ request()->routeIs('admin.likes.*') ? 'active' : '' }}" href="{{ route('admin.likes.index') }}">👍 Book Likes</a>
        <a class="nav-link {{ request()->routeIs('admin.trending.*') ? 'active' : '' }}" href="{{ route('admin.trending.index') }}">📈 Trending Books</a>
        <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">🔔 Notifications</a>
        <a class="nav-link {{ request()->routeIs('admin.onboarding.*') ? 'active' : '' }}" href="{{ route('admin.onboarding.index') }}">📱 Onboarding Screens</a>
        <a class="nav-link {{ request()->routeIs('admin.suggestions.*') ? 'active' : '' }}" href="{{ route('admin.suggestions.index') }}">💡 Book Suggestions</a>
        <a class="nav-link {{ request()->routeIs('admin.admob.*') ? 'active' : '' }}" href="{{ route('admin.admob.edit') }}">📺 Admob</a>
        <a class="nav-link {{ request()->routeIs('admin.appsettings.*') ? 'active' : '' }}" href="{{ route('admin.appsettings.edit') }}">🎛️ App Settings</a>
        <a class="nav-link {{ request()->routeIs('admin.quickshare.*') ? 'active' : '' }}" href="{{ route('admin.quickshare.edit') }}">🔗 Quick Share</a>
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">⚙️ Settings</a>

        <div class="nav-divider"></div>
        <a class="nav-link {{ request()->routeIs('admin.privacy.*') ? 'active' : '' }}" href="{{ route('admin.privacy.edit') }}">🛡️ Privacy Policy</a>
        <a class="nav-link {{ request()->routeIs('admin.terms.*') ? 'active' : '' }}" href="{{ route('admin.terms.edit') }}">📄 Terms of Uses</a>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="grid-icon">⠿</div>
            <div class="user-chip">
                <div class="avatar">👤</div>
                {{ auth('admin')->user()->name ?? 'company' }}
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<script>
    // Generic modal open/close helpers reused by every module's blade view.
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    // CSRF header for any fetch() based AJAX calls (toggles, drag-reorder, search).
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
</script>
@yield('scripts')
</body>
</html>
