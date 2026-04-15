@extends('layouts.app')

@section('title', 'Manage Users - Record Management System')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active">Manage Users</li>
@endsection

@section('styles')
<style>
    .filters {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .filters .row {
        margin-bottom: 0;
    }

    .user-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .user-table table {
        margin-bottom: 0;
    }

    .user-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }

    .user-table tbody tr:hover {
        background: #f8f9fa;
    }

    .role-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .role-badge.admin {
        background: #ffc10726;
        color: #ff6f00;
    }

    .role-badge.user {
        background: #4caf5026;
        color: #2e7d32;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .bulk-actions {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
    }

    .bulk-actions.show {
        display: block;
    }

    .bulk-actions-info {
        margin-bottom: 10px;
        font-weight: 500;
        color: #333;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="bi bi-people"></i> Manage Users</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New User
    </a>
</div>

<!-- Filter Form -->
<div class="filters">
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
        <div class="col-md-6">
            <label for="search" class="form-label">Search by Name or Email</label>
            <input type="text" class="form-control" id="search" name="search" 
                   placeholder="Search..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <label for="role" class="form-label">Filter by Role</label>
            <select class="form-select" id="role" name="role">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>
</div>

<!-- Bulk Actions -->
<div class="bulk-actions" id="bulkActionsBar">
    <div class="bulk-actions-info">
        <input type="checkbox" id="selectAllCheckbox"> 
        <span id="selectedCount">0 selected</span>
    </div>
    <form id="bulkForm" method="POST" action="{{ route('admin.users.bulk-update-role') }}" style="display: flex; gap: 10px;">
        @csrf
        <select class="form-select" id="bulkRole" name="role" style="max-width: 200px;">
            <option value="">Select Role...</option>
            <option value="admin">Make Admin</option>
            <option value="user">Make User</option>
        </select>
        <button type="submit" class="btn btn-warning">Update Selected</button>
        <input type="hidden" id="userIds" name="user_ids[]">
    </form>
</div>

<!-- Users Table -->
<div class="user-table">
    @if($users->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="selectAllPerPage"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Joined</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                        </td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role-badge {{ strtolower($user->role) }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->department->name ?? 'N/A' }}</td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-info" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                      style="display:inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
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
        <div style="padding: 20px; border-top: 1px solid #dee2e6;">
            {{ $users->links() }}
        </div>
    @else
        <div style="padding: 40px; text-align: center; color: #999;">
            <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
            <p>No users found</p>
        </div>
    @endif
</div>

<script>
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllPerPage = document.getElementById('selectAllPerPage');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    const userIds = document.getElementById('userIds');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        selectedCount.textContent = checkedBoxes.length + ' selected';
        
        if (checkedBoxes.length > 0) {
            bulkActionsBar.classList.add('show');
            userIds.value = Array.from(checkedBoxes).map(cb => cb.value).join(',');
        } else {
            bulkActionsBar.classList.remove('show');
            userIds.value = '';
        }
    }

    selectAllPerPage.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
</script>
@endsection
