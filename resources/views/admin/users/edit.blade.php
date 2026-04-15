@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Manage Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('styles')
<style>
    .edit-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 30px;
        width: 100%;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section h3 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #191e61;
        box-shadow: 0 0 0 0.2rem rgba(25, 30, 97, 0.25);
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .role-info {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        font-size: 13px;
        color: #6c757d;
        margin-top: 5px;
    }

    .role-info strong {
        display: block;
        color: #333;
        margin-bottom: 5px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .form-actions button,
    .form-actions a {
        flex: 1;
        padding: 12px;
        text-align: center;
        text-decoration: none;
    }

    .user-meta {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-top: 20px;
    }

    .user-meta p {
        margin: 8px 0;
        color: #6c757d;
        font-size: 13px;
    }

    .user-meta strong {
        color: #333;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-fill"></i> Edit User: {{ $user->name }}</h1>
</div>

<div class="edit-card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Personal Information -->
        <div class="form-section">
            <h3>Personal Information</h3>

            <div class="form-group">
                <label for="image" class="form-label">Profile Image</label>
                <div style="margin-bottom: 15px;">
                    @if($user->image)
                        <div style="margin-bottom: 12px;">
                            <img src="{{ route('profile.image', basename($user->image)) }}" 
                                 alt="{{ $user->name }}" 
                                 style="max-width: 150px; height: 150px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                            <p style="font-size: 12px; color: #6c757d; margin-top: 8px;">Current profile image</p>
                        </div>
                    @else
                        <div style="background: #f8f9fa; width: 150px; height: 150px; border-radius: 6px; border: 1px solid #dee2e6; display: flex; align-items: center; justify-content: center; color: #6c757d; margin-bottom: 12px;">
                            <i class="bi bi-image" style="font-size: 32px;"></i>
                        </div>
                        <p style="font-size: 12px; color: #6c757d; margin-top: 8px;">No profile image</p>
                    @endif
                </div>
                <input 
                    type="file"
                    class="form-control @error('image') is-invalid @enderror"
                    id="image" 
                    name="image"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                >
                <small style="display: block; color: #6c757d; margin-top: 8px;">Accepted formats: JPEG, PNG, GIF, WebP. Max size: 2MB</small>
                @error('image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror"
                    id="name" 
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                >
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror"
                    id="email" 
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    required
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Role Management -->
        <div class="form-section">
            <h3>Role & Permissions</h3>

            <div class="form-group">
                <label for="role" class="form-label">User Role</label>
                <select 
                    class="form-select @error('role') is-invalid @enderror"
                    id="role" 
                    name="role"
                    required
                >
                    <option value="">-- Select Role --</option>
                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>
                        User
                    </option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                        Administrator
                    </option>
                </select>
                @error('role')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <div class="role-info">
                    <strong id="roleDescription">Regular User</strong>
                    <span id="roleDetails">Can create and manage documents, view transactions.</span>
                </div>
            </div>
        </div>

        <!-- User Information -->
        <div class="user-meta">
            <p><strong>User ID:</strong> #{{ $user->id }}</p>
            <p><strong>Created:</strong> {{ $user->created_at->format('M d, Y \a\t H:i') }}</p>
            <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y \a\t H:i') }}</p>
            <p><strong>Department:</strong> {{ $user->department->name ?? 'Not assigned' }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Save Changes
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
    const roleSelect = document.getElementById('role');
    const roleDescription = document.getElementById('roleDescription');
    const roleDetails = document.getElementById('roleDetails');

    const roleDescriptions = {
        'user': {
            title: 'Regular User',
            description: 'Can create and manage documents, view transactions, and manage their profile.'
        },
        'admin': {
            title: 'Administrator',
            description: 'Full access to all features, user management, system settings, and reports.'
        }
    };

    roleSelect.addEventListener('change', function() {
        const role = this.value;
        if (roleDescriptions[role]) {
            roleDescription.textContent = roleDescriptions[role].title;
            roleDetails.textContent = roleDescriptions[role].description;
        }
    });

    // Trigger change event on load to show current role description
    roleSelect.dispatchEvent(new Event('change'));
</script>
@endsection
