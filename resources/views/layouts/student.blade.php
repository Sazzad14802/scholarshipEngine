<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Scholarship Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f8; }
        .sidebar {
            min-height: 100vh;
            background: #14532d;
            color: #fff;
            width: 240px;
            position: fixed;
            top: 0; left: 0;
        }
        .sidebar .brand {
            padding: 20px 16px 14px;
            font-size: 1rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar .nav-link {
            color: #bbf7d0;
            padding: 10px 16px;
            border-radius: 6px;
            margin: 2px 8px;
            font-size: .9rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }
        .sidebar .nav-link i { margin-right: 8px; }
        .main-content {
            margin-left: 240px;
            padding: 24px;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 24px;
            margin-left: 240px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar d-flex flex-column">
    <div class="brand">
        <i class="bi bi-mortarboard-fill me-2"></i>Scholarship Engine
        <div class="text-muted small fw-normal mt-1" style="color:#86efac!important">Student Portal</div>
    </div>
    <nav class="nav flex-column mt-2">
        <a href="{{ route('student.dashboard') }}"
           class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i>Dashboard
        </a>
        <a href="{{ route('student.scholarships.index') }}"
           class="nav-link {{ request()->routeIs('student.scholarships*') ? 'active' : '' }}">
            <i class="bi bi-search"></i>Browse Scholarships
        </a>

        <a href="{{ route('student.profile.show') }}"
           class="nav-link {{ request()->routeIs('student.profile*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>My Profile
        </a>
    </nav>
    <div class="mt-auto p-3 border-top border-secondary">
        <div class="small mb-1" style="color:#86efac">{{ Auth::user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</div>

{{-- Top bar --}}
<div class="topbar">
    <h6 class="mb-0 fw-semibold">@yield('title', 'Dashboard')</h6>
    <span class="badge bg-success">Student</span>
</div>

{{-- Main content --}}
<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
