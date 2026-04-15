@extends('layouts.app')

@section('title', 'Manage Departments - Record Management System')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active">Manage Departments</li>
@endsection

@section('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        margin: 0;
    }

    .filters {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .department-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .department-table table {
        margin-bottom: 0;
    }

    .department-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }

    .department-table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-badge.active {
        background: #4caf5026;
        color: #2e7d32;
    }

    .status-badge.inactive {
        background: #f4433626;
        color: #c62828;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .alert {
        border-radius: 8px;
        border: none;
        padding: 12px 16px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .users-count {
        font-size: 12px;
        color: #6c757d;
    }

</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-diagram-3"></i> Manage Departments</h1>
    </div>
    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Department
    </a>
</div>

<!-- Flash Messages -->
@if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filter Form -->
<div class="filters">
    <form method="GET" action="{{ route('admin.departments.index') }}" class="row g-3">
        <div class="col-md-10">
            <label for="search" class="form-label">Search by Name or Code</label>
            <input type="text" class="form-control" id="search" name="search" 
                   placeholder="Search..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>
</div>

<!-- Departments Table -->
<div class="department-table">
    @if($departments->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Users</th>
                    <th>Documents</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                    <tr>
                        <td>
                            <strong>{{ $department->name }}</strong>
                        </td>
                        <td>
                            {{ $department->code ?? '-' }}
                        </td>
                        <td>
                            <small>{{ Str::limit($department->description, 50) ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="status-badge {{ $department->is_active ? 'active' : 'inactive' }}">
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <span class="users-count">{{ $department->users()->count() }} users</span>
                        </td>
                        <td>
                            <span class="users-count">{{ $department->documents()->count() }} docs</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" 
                                      style="display:inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this department?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted">
                    Showing <strong>{{ ($departments->currentPage() - 1) * $departments->perPage() + 1 }}</strong> 
                    to <strong>{{ min($departments->currentPage() * $departments->perPage(), $departments->total()) }}</strong> 
                    of <strong>{{ $departments->total() }}</strong> records
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <nav aria-label="Page navigation">
                    {{ $departments->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    @else
        <div style="padding: 40px; text-align: center; color: #999;">
            <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
            <p>No departments found</p>
        </div>
    @endif
</div>

<script>
    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    });
</script>
@endsection
