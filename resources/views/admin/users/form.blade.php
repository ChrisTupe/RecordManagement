@extends('layouts.app')

@section('title', isset($user) ? 'Edit User - ' . $user->name : 'Create New User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Manage Users</a></li>
    <li class="breadcrumb-item active">{{ isset($user) ? 'Edit' : 'New User' }}</li>
@endsection

@section('styles')
<style>
    .form-card {
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
        display: block;
    }

    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
        width: 100%;
    }

    .form-control:focus, .form-select:focus {
        border-color: #191e61;
        box-shadow: 0 0 0 0.2rem rgba(25, 30, 97, 0.25);
        outline: none;
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
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.3s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #191e61 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #e0e0e0;
        color: #333;
    }

    .btn-secondary:hover {
        background: #d0d0d0;
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

    .password-note {
        background: #e3f2fd;
        padding: 10px;
        border-radius: 6px;
        font-size: 12px;
        color: #1565c0;
        margin-top: 5px;
    }

    .password-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-input-wrapper input {
        flex: 1;
        padding-right: 45px;
    }

    .password-toggle-btn {
        position: absolute;
        right: 15px;
        background: none;
        border: none;
        cursor: pointer;
        color: #191e61;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        width: 30px;
        height: 30px;
    }

    .password-toggle-btn:hover {
        color: #764ba2;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1>
        <i class="bi bi-person-plus"></i> 
        {{ isset($user) ? 'Edit User: ' . $user->name : 'Create New User' }}
    </h1>
</div>

<div class="form-card">
    <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <!-- Personal Information -->
        <div class="form-section">
            <h3>Personal Information</h3>

            <div class="form-group">
                <label for="image" class="form-label">Profile Image</label>
                <div style="margin-bottom: 15px;">
                    @if(isset($user) && $user->image)
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
                        <p style="font-size: 12px; color: #6c757d; margin-top: 8px;">{{ isset($user) ? 'No profile image' : 'No image selected' }}</p>
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
                <label for="name" class="form-label">Full Name <span style="color: #dc3545;">*</span></label>
                <input 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror"
                    id="name" 
                    name="name"
                    value="{{ old('name', $user->name ?? '') }}"
                    placeholder="Enter full name"
                    required
                >
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address <span style="color: #dc3545;">*</span></label>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror"
                    id="email" 
                    name="email"
                    value="{{ old('email', $user->email ?? '') }}"
                    placeholder="Enter email address"
                    required
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            @if(!isset($user))
                <div class="form-group">
                    <label for="password" class="form-label">Password <span style="color: #dc3545;">*</span></label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror"
                            id="password" 
                            name="password"
                            placeholder="Minimum 8 characters"
                            required
                        >
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password')" title="Show/Hide password">
                            <i class="bi bi-eye" id="password-icon"></i>
                        </button>
                    </div>
                    <div class="password-note">
                        <i class="bi bi-info-circle"></i> Password must be at least 8 characters long
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password <span style="color: #dc3545;">*</span></label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation" 
                            name="password_confirmation"
                            placeholder="Confirm password"
                            required
                        >
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_confirmation')" title="Show/Hide password">
                            <i class="bi bi-eye" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        </div>

        <!-- Department & Role -->
        <div class="form-section">
            <h3>Department & Role Assignment</h3>

            <div class="form-group">
                <label for="department_id" class="form-label">Department</label>
                <select 
                    class="form-select @error('department_id') is-invalid @enderror"
                    id="department_id" 
                    name="department_id"
                >
                    <option value="">-- Select Department (Optional) --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id ?? null) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}{{ $dept->code ? ' (' . $dept->code . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="role" class="form-label">User Role <span style="color: #dc3545;">*</span></label>
                <select 
                    class="form-select @error('role') is-invalid @enderror"
                    id="role" 
                    name="role"
                    required
                >
                    <option value="">-- Select Role --</option>
                    <option value="user" {{ old('role', $user->role ?? 'user') === 'user' ? 'selected' : '' }}>
                        User
                    </option>
                    <option value="admin" {{ old('role', $user->role ?? null) === 'admin' ? 'selected' : '' }}>
                        Administrator
                    </option>
                </select>
                @error('role')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <div class="role-info">
                    <strong id="roleDescription">Regular User</strong>
                    <span id="roleDetails">Can create and manage documents, view transactions, and manage their profile.</span>
                </div>
            </div>
        </div>

        <!-- User Information (only for editing) -->
        @if(isset($user))
            <div class="user-meta">
                <p><strong>User ID:</strong> #{{ $user->id }}</p>
                <p><strong>Created:</strong> {{ $user->created_at->format('M d, Y \a\t H:i') }}</p>
                <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y \a\t H:i') }}</p>
                <p><strong>Current Department:</strong> {{ $user->department->name ?? 'Not assigned' }}</p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> {{ isset($user) ? 'Update User' : 'Create User' }}
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

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
