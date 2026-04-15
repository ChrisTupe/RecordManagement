<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Record Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #282974;
            --primary-color-dark: #1f2059;
            --primary-color-rgba: rgba(40, 41, 116, 0.25);
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .wrapper {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary-color);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar .brand {
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar .nav {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
            font-weight: 600;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .sidebar-footer .nav-link {
            padding: 12px 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-footer .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }

        .sidebar-footer .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
            font-weight: 600;
        }

        .sidebar-footer .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .sidebar-footer .user-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .sidebar-footer .user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-footer .user-name {
            font-weight: 600;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .sidebar-footer .user-email {
            font-size: 11px;
            color: rgba(255,255,255,0.7);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .sidebar-footer .nav-link:last-child {
            margin-bottom: 0;
        }

        .sidebar-footer .logout-btn {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin-bottom: 0;
            width: 100%;
            border: none;
            background: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            text-align: left;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-footer .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .topbar .breadcrumb {
            margin: 0;
            padding: 0;
        }

        .topbar .breadcrumb-item {
            color: var(--primary-color);
        }

        .topbar .breadcrumb-item.active {
            color: #6c757d;
        }

        .content-area {
            padding: 30px;
            flex: 1;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-color-dark);
            border: none;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0;
            font-weight: 600;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .table {
            margin: 0;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 6px 12px;
            font-weight: 500;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem var(--primary-color-rgba);
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .tabs-container {
            margin-top: 30px;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 3px solid transparent;
            font-weight: 500;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background: none;
            border-bottom-color: var(--primary-color);
        }

        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0;
            border: none;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
               Record Management
            </div>
            <nav class="nav">
                <!-- User Dashboard -->
                @if(isUser())
                    <a href="{{ route('documents.index') }}" class="nav-link {{ request()->routeIs('documents.index') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a href="{{ route('documents.list') }}" class="nav-link {{ request()->routeIs('documents.list') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i> Documents
                    </a>
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i> Transactions
                    </a>
                @endif

                <!-- Admin Dashboard -->
                @if(isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i> Admin Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i> Manage Departments
                    </a>
                @endif
            </nav>
            <div class="sidebar-footer">
                <div class="user-profile">
                    @if(auth()->user()->image)
                        <img src="{{ route('profile.image', basename(auth()->user()->image)) }}" alt="Profile" class="user-image">
                    @else
                        <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,0.3);">
                            <i class="bi bi-person" style="font-size: 24px;"></i>
                        </div>
                    @endif
                    <div class="user-info">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-email">{{ auth()->user()->email }}</span>
                    </div>
                </div>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
                <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @yield('breadcrumbs')
                    </ol>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Validation Errors:</strong>
                        <ul class="mb-0 ms-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Close alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    @yield('scripts')
</body>
</html>