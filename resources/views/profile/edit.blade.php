@extends('layouts.app')

@section('title', 'Edit Profile - Record Management System')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profile</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('styles')
<style>
    .profile-card {
        width: 100%;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .readonly-field {
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        padding: 10px 25px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #191e61 0%, #764ba2 100%);
        color: white;
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #e0e0e0;
        color: #333;
        border: none;
    }

    .btn-secondary:hover {
        background: #d0d0d0;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .form-section h2 {
        font-size: 18px;
        color: #333;
        margin-bottom: 20px;
        font-weight: 600;
    }

    @media (max-width: 600px) {
        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil-square"></i> Edit Profile</h1>
</div>

<div class="card profile-card">
    <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h2>Personal Information</h2>

                <div class="form-group">
                    <label for="image" class="form-label">Profile Image</label>
                    <div style="margin-bottom: 10px;">
                        @if($user->image)
                            <img src="{{ route('profile.image', basename($user->image)) }}" alt="Profile Image" style="width: 100px; height: 100px; border-radius: 8px; object-fit: cover;">
                        @else
                            <div style="width: 100px; height: 100px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999;">
                                No Image
                            </div>
                        @endif
                    </div>
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/*"
                    >
                    <small style="color: #999; margin-top: 5px; display: block;">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                    @error('image')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control @error('name') is-invalid @enderror"
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
                        id="email" 
                        name="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        required
                    >
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h2>Department Information</h2>

                <div class="form-group">
                    <label for="department" class="form-label">Department</label>
                    <input 
                        type="text" 
                        id="department" 
                        class="form-control readonly-field"
                        value="{{ $user->department->name ?? 'Not assigned' }}"
                        readonly
                    >
                    <small style="color: #999; margin-top: 5px; display: block;">Department assignment is managed by administrators</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
                <a href="{{ route('profile.show') }}" class="btn btn-secondary" style="text-decoration: none;">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
