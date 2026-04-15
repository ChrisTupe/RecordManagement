@extends('layouts.app')

@section('title', 'Admin Dashboard - Record Management System')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Admin Dashboard</li>
@endsection

@section('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }

    .stat-icon.users {
        background: rgba(102, 126, 234, 0.1);
        color: #191e61;
    }

    .stat-icon.admins {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .stat-icon.documents {
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
    }

    .stat-icon.transactions {
        background: rgba(233, 30, 99, 0.1);
        color: #e91e63;
    }

    .stat-content h3 {
        margin: 0 0 5px 0;
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }

    .stat-content .number {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    .section-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .section-card h2 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .user-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        gap: 15px;
    }

    .user-item:last-child {
        border-bottom: none;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        color: #333;
        display: block;
        margin-bottom: 3px;
    }

    .user-email {
        font-size: 13px;
        color: #6c757d;
    }

    .role-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .role-badge.admin {
        background: #ffc10726;
        color: #ff6f00;
    }

    .role-badge.user {
        background: #4caf5026;
        color: #2e7d32;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.active {
        background: #4caf5033;
        color: #2e7d32;
    }

    .admin-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .quick-link-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: linear-gradient(135deg, #191e61 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .quick-link-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .quick-link-btn i {
        font-size: 24px;
    }

    @media (max-width: 768px) {
        .stat-card {
            flex-direction: column;
            text-align: center;
        }

        .stat-card .stat-icon {
            margin-right: 0;
        }

        .stat-content .number {
            font-size: 24px;
        }
    }

    /* Dashboard sidebar footer customization */
    .sidebar-footer .user-name {
        color: rgba(255, 255, 255, 0.9);
    }

    .sidebar-footer .user-email {
        color: rgba(255, 255, 255, 0.6);
    }

    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 20px;
    }
</style>
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="bi bi-shield-check"></i> Admin Control Panel</h1>
    <p style="color: #6c757d; margin: 0;">Manage users, monitor system activity, and control application settings</p>
</div>

<!-- System Statistics -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon users">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-content">
                <h3>Total Users</h3>
                <p class="number">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon admins">
                <i class="bi bi-shield-fill"></i>
            </div>
            <div class="stat-content">
                <h3>Administrators</h3>
                <p class="number">{{ $admins }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon documents">
                <i class="bi bi-files"></i>
            </div>
            <div class="stat-content">
                <h3>Total Documents</h3>
                <p class="number">{{ $totalDocuments ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon transactions">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <div class="stat-content">
                <h3>Total Transactions</h3>
                <p class="number">{{ $totalTransactions ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Department Charts -->
<div class="row">
    <div class="col-lg-6">
        <div class="section-card">
            <h2>Users per Department</h2>
            <div class="chart-container">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="section-card">
            <h2>Documents per Department</h2>
            <div class="chart-container">
                <canvas id="documentsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- System Overview -->
<div class="section-card">
    <h2>System Overview</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
        <div>
            <p style="color: #6c757d; margin: 0 0 5px 0; font-size: 13px;">
                <strong>Regular Users</strong>
            </p>
            <p style="margin: 0; font-size: 24px; font-weight: 700; color: #333;">
                {{ $totalUsers - $admins }}
            </p>
        </div>
        <div>
            <p style="color: #6c757d; margin: 0 0 5px 0; font-size: 13px;">
                <strong>Admin Accounts</strong>
            </p>
            <p style="margin: 0; font-size: 24px; font-weight: 700; color: #333;">
                {{ $admins }}
            </p>
        </div>
        <div>
            <p style="color: #6c757d; margin: 0 0 5px 0; font-size: 13px;">
                <strong>System Status</strong>
            </p>
            <span class="status-badge active" style="margin-top: 5px;">
                <i class="bi bi-check-circle-fill" style="margin-right: 5px;"></i>Operational
            </span>
        </div>
        <div>
            <p style="color: #6c757d; margin: 0 0 5px 0; font-size: 13px;">
                <strong>Database</strong>
            </p>
            <span class="status-badge active" style="margin-top: 5px;">
                <i class="bi bi-database" style="margin-right: 5px;"></i>Connected
            </span>
        </div>
    </div>
</div>

<!-- Recent Users -->
<div class="section-card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Recent User Registrations</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">View All</a>
    </div>

    @if($recentUsers->count() > 0)
        @foreach($recentUsers as $user)
            <div class="user-item">
                <div class="user-info">
                    <span class="user-name">{{ $user->name }}</span>
                    <span class="user-email">{{ $user->email }}</span>
                </div>
                <span class="role-badge {{ strtolower($user->role) }}">
                    {{ ucfirst($user->role) }}
                </span>
                <span class="status-badge active">
                    Joined {{ $user->created_at->diffForHumans() }}
                </span>
            </div>
        @endforeach
    @else
        <p style="text-align: center; color: #999; margin: 20px 0;">No users yet</p>
    @endif
</div>

<!-- Quick Actions -->
<div class="section-card">
    <h2>Quick Actions</h2>
    <div class="quick-links">
        <a href="{{ route('admin.users.index') }}" class="quick-link-btn">
            <i class="bi bi-people"></i>
            <span>Manage Users</span>
        </a>
        <a href="{{ route('admin.departments.index') }}" class="quick-link-btn" style="background: linear-gradient(135deg, #191e61 0%, #764ba2 100%);">
            <i class="bi bi-diagram-3"></i>
            <span>Manage Departments</span>
        </a>
        <a href="{{ route('admin.users.export') }}" class="quick-link-btn" style="background: linear-gradient(135deg, #191e61 0%, #764ba2 100%);">
            <i class="bi bi-download"></i>
            <span>Export Users</span>
        </a>
    </div>
</div>

<script>
    // Users per Department Chart
    const userDepartments = @json($usersPerDepartment->pluck('name'));
    const userCounts = @json($usersPerDepartment->pluck('users_count'));

    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'bar',
        data: {
            labels: userDepartments,
            datasets: [{
                label: 'Number of Users',
                data: userCounts,
                backgroundColor: 'rgba(102, 126, 234, 0.6)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Documents per Department Chart
    const docDepartments = @json($documentsPerDepartment->pluck('name'));
    const docCounts = @json($documentsPerDepartment->pluck('documents_count'));

    const docsCtx = document.getElementById('documentsChart').getContext('2d');
    new Chart(docsCtx, {
        type: 'bar',
        data: {
            labels: docDepartments,
            datasets: [{
                label: 'Number of Documents',
                data: docCounts,
                backgroundColor: 'rgba(76, 175, 80, 0.6)',
                borderColor: 'rgba(76, 175, 80, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endsection
